<?php

namespace App\Http\Controllers;

use App\Helper\EmailMarketingHelper;
use App\Models\CampaignLog;
use App\Models\CampaignProperty;
use App\Models\CampaignRecipient;
use App\Models\Contact;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\Storage;
use App\Models\AwsS3History;
use App\Models\Campaign;
use App\Services\EmailService;
use Illuminate\Validation\Rule;

class EmailMarketingController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('email-marketing.contacts');
    }
    public function contacts(Request $request)
    {

        $perPage = $request->input('per_page', 10); // 10, 25, 50, 100, 250, 500
        $reqSearch = $request->input('search'); // name, email, phone
        $reqCounty = $request->input('county');
        $reqDealType = $request->input('deal_type');
        $reqZip = $request->input('zip');
        $reqTag = $request->input('tag');

        $contacts = Contact::where('user_id', $request->user()->id)
            ->when($reqSearch, function ($query) use ($reqSearch) {
                $query->where(function ($q) use ($reqSearch) {
                    $q->where('first_name', 'ilike', "%{$reqSearch}%")
                        ->orWhere('last_name', 'ilike', "%{$reqSearch}%")
                        ->orWhere('email', 'ilike', "%{$reqSearch}%")
                        ->orWhere('phone', 'like', "%{$reqSearch}%");
                });
            })
            ->when($reqCounty, function ($query) use ($reqCounty) {
                $query->where('counties', 'like', '%' . $reqCounty . '%');
            })
            ->when($reqDealType, function ($query) use ($reqDealType) {
                $query->where('deal_type', 'like', '%' . $reqDealType . '%');
            })
            ->when($reqZip, function ($query) use ($reqZip) {
                $query->where('zip', 'like', '%' . $reqZip . '%');
            })
            ->when($reqTag, function ($query) use ($reqTag) {
                $query->where('tags', 'like', '%' . $reqTag . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        $contacts->getCollection()->transform(function ($contact) use ($request) {
            $contact->has_contact_info = $contact->hasContactInfo();
            return $contact;
        });


        $counties = EmailMarketingHelper::getCounties($request->user()->id);
        $dealTypes = EmailMarketingHelper::getDealTypes($request->user()->id);
        $zipCodes = EmailMarketingHelper::getZips($request->user()->id);
        $tags = EmailMarketingHelper::getTags($request->user()->id);

        return Inertia::render('email-marketing/contact/ContactsPage', [
            'contacts' => $contacts,
            'counties' => $counties,
            'dealTypes' => $dealTypes,
            'uploadErrors' => $request->session()->get('uploadErrors'),
            'zipCodes' => $zipCodes,
            'tags' => $tags,
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }
    public function deleteContact(Request $request, $id)
    {
        $contact = Contact::where('user_id', $request->user()->id)->find(id: $id);
        if ($contact) {
            $contact->delete();
            return redirect()->route('email-marketing.contacts')->with('success', 'Contact deleted successfully');
        }
        return redirect()->route('email-marketing.contacts')->with('error', 'Contact not found');
    }

    public function deleteSelectedContacts(Request $request)
    {
        $result = EmailMarketingHelper::deleteSelectedContacts($request);
        if ($result) {
            return redirect()->route('email-marketing.contacts')->with('success', 'Contacts deleted successfully');
        } else {
            return redirect()->route('email-marketing.contacts')->with('error', 'Contacts not found');
        }
    }


    public function uploadContacts(Request $request)
    {

        set_time_limit(600); // 10 minutes
        ini_set('memory_limit', '2048M'); // 2GB

        $request->validate([
            'file' => 'required|mimes:csv,text/plain|max:102400', // 100MB 
        ]);

        $file = $request->file('file');
        $userId = $request->user()->id;

        if ($request->hasFile('file')) {
            try {
                // Validate header columns first
                $handle = fopen($file->getPathname(), 'r');
                $header = fgetcsv($handle);
                fclose($handle);

                // Remove BOM from first column if present
                if ($header && isset($header[0])) {
                    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]); // Remove UTF-8 BOM
                    $header[0] = ltrim($header[0], "\xEF\xBB\xBF\xFE\xFF"); // Remove BOM characters
                }


                // Define required columns in order
                $requiredColumns = [
                    'email',
                    'tags',
                    'phone_number',
                    'first_name',
                    'last_name',
                    'counties_invest',
                    'deal_type',
                    'zip_code_invest'
                ];

                // Validate column count
                if (count($header) < count($requiredColumns)) {
                    return redirect()->back()->withErrors([
                        'file' => 'File must have at least ' . count($requiredColumns) . ' <br/><br/><strong>Required Columns:</strong> ' . implode(', ', $requiredColumns) . ' <br/><br/><strong>Uploaded columns:</strong> ' . implode(', ', $header)
                    ]);
                }

                // Validate column names and order
                for ($i = 0; $i < count($requiredColumns); $i++) {
                    if (!isset($header[$i]) || $header[$i] !== $requiredColumns[$i]) {
                        return redirect()->back()->withErrors([
                            'file' => 'File must have the following columns in order: ' . implode(', ', $requiredColumns)
                        ]);
                    }
                }

                // Store the validated header for later use
                $validatedHeader = $header;

                // Upload file to S3
                $path = "contacts/" . $userId . '/';
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $fullPath = $path . $filename;

                Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');
                $url = Storage::disk('s3')->url($fullPath);
                AwsS3History::addHistory('contacts', $fullPath, $url, $request->user()->id);

                if ($url) {
                    // Process CSV import with error handling
                    $batchSize = 1000;
                    $rows = [];
                    $header = null;
                    $created = 0;
                    $updated = 0;
                    $skipped = 0;
                    $errors = [];

                    if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                        $lineNumber = 0;

                        while (($row = fgetcsv($handle)) !== false) {
                            $lineNumber++;

                            try {
                                // Skip empty lines
                                if (count($row) === 1 && trim($row[0]) === '') {
                                    continue;
                                }

                                // Remove BOM from first column if present
                                if ($row && isset($row[0])) {
                                    $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                                    $row[0] = ltrim($row[0], "\xEF\xBB\xBF\xFE\xFF");
                                }

                                if (!$header) {
                                    $header = $row;
                                    continue;
                                }

                                // Ensure we have the same number of columns as the header
                                if (count($row) !== count($header)) {
                                    $skipped++;
                                    $errors[] = "Line {$lineNumber}: Column count mismatch (expected " . count($header) . ", got " . count($row) . ")";
                                    continue;
                                }

                                $data = array_combine($header, $row);

                                // Validate email
                                $email = trim($data['email'] ?? '');
                                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $skipped++;
                                    $errors[] = "Line {$lineNumber}: Invalid email format";
                                    continue;
                                }

                                $rows[] = $data;

                                if (count($rows) >= $batchSize) {
                                    // Process batch
                                    [$c, $u] = $this->processContactBatch($rows, $userId);
                                    $created += $c;
                                    $updated += $u;
                                    $rows = [];
                                }
                            } catch (\Exception $e) {
                                $skipped++;
                                $errors[] = "Line {$lineNumber}: " . $e->getMessage();
                                continue; // Skip this record and continue with next
                            }
                        }

                        // Process any remaining rows
                        if (count($rows)) {
                            try {
                                [$c, $u] = $this->processContactBatch($rows, $userId);
                                $created += $c;
                                $updated += $u;
                            } catch (\Exception $e) {
                                $errors[] = "Batch processing error: " . $e->getMessage();
                            }
                        }

                        fclose($handle);


                        $created = number_format($created);
                        $updated = number_format($updated);
                        $skipped = number_format($skipped);
                        // Prepare success message
                        $message = "File uploaded successfully! <br/><strong>{$created}</strong> contacts created. <br/><strong>{$updated}</strong> contacts updated. <br/><strong>{$skipped}</strong> records skipped due to errors.";

                        return redirect()->back()->with('success', $message)->with('uploadErrors', $errors);
                    }
                }

                return redirect()->back()->with('error', 'Couldn\'t open the file.');
            } catch (\Exception $e) {
                AwsS3History::addHistory('contacts', null, null, $request->user()->id, $e->getMessage());
                return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'File could not be uploaded');
    }



    private function processContactBatch(array $rows, int $userId): array
    {
        $created = 0;
        $updated = 0;

        $fieldMap = [
            'tags' => 'tags',
            'counties_invest' => 'counties',
            'deal_type' => 'deal_type',
            'zip_code_invest' => 'zip',

        ];

        foreach ($rows as $row) {
            try {
                // Clean and normalize data
                foreach ($row as $key => &$value) {
                    $value = is_string($value) ? trim($value) : $value;

                    if (array_key_exists($key, $fieldMap)) {
                        if (is_array($value)) {
                            // ok
                        } elseif ($value === '' || $value === null) {
                            $value = [];
                        } else {
                            $value = array_filter(array_map('trim', explode(',', str_replace('"', '', $value))));
                        }
                    }
                }

                // Validate email again before processing
                $email = trim($row['email'] ?? '');
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue; // Skip invalid emails
                }

                // Clean phone number
                $phone = trim($row['phone_number'] ?? '');
                if (!empty($phone)) {
                    // Remove all non-numeric characters except +, -, (, ), and spaces
                    $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);
                    // Limit to reasonable length
                    if (strlen($phone) > 20) {
                        $phone = substr($phone, 0, 20);
                    }
                }

                $contact = Contact::where('email', $email)->where('user_id', $userId)->first();

                if ($contact) {
                    // Update existing contact
                    foreach ($fieldMap as $input => $column) {
                        $incoming = $row[$input] ?? [];

                        // Force cast to array to avoid string input
                        $incoming = is_array($incoming) ? $incoming : [];

                        $existing = $contact->{$column} ?? [];
                        $existing = is_array($existing) ? $existing : [];

                        $contact->{$column} = array_values(array_unique([...$existing, ...$incoming]));
                    }

                    $contact->fill([
                        'name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                        'phone' => $phone,
                        'first_name' => trim($row['first_name'] ?? ''),
                        'last_name' => trim($row['last_name'] ?? ''),
                    ])->save();

                    $updated++;
                } else {
                    // Create new contact
                    $newContact = new Contact();
                    $newContact->fill([
                        'email' => $email,
                        'name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                        'phone' => $phone,
                        'first_name' => trim($row['first_name'] ?? ''),
                        'last_name' => trim($row['last_name'] ?? ''),
                        'user_id' => $userId,
                    ]);

                    foreach ($fieldMap as $input => $column) {
                        $val = $row[$input] ?? [];
                        $newContact->{$column} = is_array($val) ? $val : [];
                    }

                    $newContact->save();
                    $created++;
                }
            } catch (\Exception $e) {
                // Log error and continue with next record
                \Log::warning('Contact import error: ' . $e->getMessage(), [
                    'email' => $row['email'] ?? 'unknown',
                    'user_id' => $userId
                ]);
                continue;
            }
        }

        return [$created, $updated];
    }


    public function saveContact(Request $request)
    {
        $queryParamString = $request->input('page_query_params');
        parse_str($queryParamString, $queryParams);

        $request->validate([
            'contactId' => 'nullable|integer',
            'email' => 'required|email',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'phone' => 'nullable',
            'tags' => 'nullable|array',
            'counties' => 'nullable|array',
            'zip' => 'nullable|array',
            'deal_type' => 'nullable|array'
        ]);

        $contact = Contact::where('id', $request->contactId)->where('user_id', $request->user()->id)->first();

        if ($contact) {
            // Update existing contact
            $request->merge(['name' => trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''))]);
            $contact->update($request->except('contactId', 'user_id'));

            // Track ownership of tags for existing contact
            $this->trackTagOwnership($contact, $request->tags ?? [], $request->user()->id);
        } else {
            // Create new contact
            $request->merge(['name' => trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''))]);
            $request->merge(['user_id' => $request->user()->id]);
            $contact = Contact::create($request->except('contactId'));
            $contact->save();

            // Track ownership of tags for new contact
            $this->trackTagOwnership($contact, $request->tags ?? [], $request->user()->id);
        }

        return redirect()->route('email-marketing.contacts', $queryParams)->with('success', 'Contact saved successfully');
    }

    // Add this new method to track tag ownership
    private function trackTagOwnership($contact, $tags, $userId)
    {
        // Remove existing ownership entries for this contact and user
        \App\Models\ContactTagOwnership::where('contact_id', $contact->id)
            ->where('user_id', $userId)
            ->delete();

        // Add ownership for each tag
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                \App\Models\ContactTagOwnership::create([
                    'contact_id' => $contact->id,
                    'tag_name' => $tag,
                    'user_id' => $userId,
                    'is_public' => ($tag === 'Skip-Traced') // Only Skip-Traced is public
                ]);
            }
        }
    }

    public function getContacts(Request $request)
    {

        $byTag = (array) $request->input('tags', []);
        $byCounty = (array) $request->input('counties', []);
        $byDealType = (array) $request->input('deal_type', []);
        $byZip = (array) $request->input('zip', []);

        $contacts = Contact::where('user_id', $request->user()->id);

        if (!empty($byTag)) {
            $contacts->whereJsonContains('tags', $byTag);
        }

        if (!empty($byCounty)) {
            $contacts->whereJsonContains('counties', $byCounty);
        }

        if (!empty($byDealType)) {
            $contacts->whereJsonContains('deal_type', $byDealType);
        }

        if (!empty($byZip)) {
            $contacts->whereJsonContains('zip', $byZip);
        }

        $contacts = $contacts->orderBy('updated_at', 'desc')->get();

        return response()->json($contacts);

    }

    public function getAvailableOptions(Request $request)
    {
        $counties = EmailMarketingHelper::getCounties($request->user()->id);
        $dealTypes = EmailMarketingHelper::getDealTypes($request->user()->id);
        $zipCodes = EmailMarketingHelper::getZips($request->user()->id);
        $tags = EmailMarketingHelper::getTags($request->user()->id);

        return response()->json([
            'counties' => $counties,
            'deal_types' => $dealTypes,
            'zip_codes' => $zipCodes,
            'tags' => $tags,
        ]);
    }

    // Campaign routes

    public function campaign(Request $request)
    {
        $campaigns = Campaign::where('user_id', $request->user()->id)
            ->withCount([
                'recipients',
                'recipients as sent_count' => function ($query) {
                    $query->where('status', 'sent');
                },
                'recipients as failed_count' => function ($query) {
                    $query->where('status', 'failed');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: 2);

        // Calculate open and click rates (placeholder - would need email tracking)
        $campaigns->getCollection()->transform(function ($campaign) {
            $campaign->open_rate = 0; // Would calculate from email tracking
            $campaign->click_rate = 0; // Would calculate from email tracking
            return $campaign;
        });

        $paramPropertyID = $request->get('property_id');

        return Inertia::render('email-marketing/campaign/CampaignPage', [
            'campaigns' => $campaigns,
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'mustVerifyCompanyEmail' => $request->user()->company_email && !$request->user()->hasVerifiedCompanyEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
            'user' => $request->user(),
            'property_id' => $paramPropertyID ?? null,
        ]);
    }

    public function sendCampaign(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'headerText' => 'nullable|string',
            'contactSelection' => 'required|in:all,filtered,selected',
            'contactFilters' => 'array',
            'contactFilters.counties' => 'array',
            'contactFilters.dealTypes' => 'array',
            'contactFilters.tags' => 'array',
            'contactFilters.zipCodes' => 'array',
            'selectedContacts' => 'array',
            'propertyData' => 'required|array',
            'schedule' => 'array',
            'schedule.sendImmediately' => 'required|boolean',
            'schedule.scheduledDate' => [
                Rule::requiredIf(!$request->input('schedule.sendImmediately')),
                'string',
                'nullable',
            ],
            'schedule.scheduledTime' => [
                Rule::requiredIf(!$request->input('schedule.sendImmediately')),
                'string',
                'nullable',
            ],
        ]);


        try {
            DB::beginTransaction();

            // Create campaign record
            $campaign = Campaign::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'subject' => $request->subject,
                'email_header' => $request->headerText,
                'email_description' => $request->description,
                'property_id' => $request->propertyData['id'],
                'property_data' => $request->propertyData,
                'status' => 'draft',
                'scheduled_at' => $request->schedule['sendImmediately'] ? null :
                    Carbon::parse($request->schedule['scheduledDate'] . ' ' . $request->schedule['scheduledTime']),
            ]);

            // Get recipients based on selection
            $recipients = $this->getRecipients($request);

            // Create recipient records
            foreach ($recipients as $recipient) {
                CampaignRecipient::create([
                    'campaign_id' => $campaign->id,
                    'contact_id' => $recipient->id,
                    'email' => $recipient->email,
                    'name' => $recipient->name,
                    'status' => 'pending',
                ]);
            }
            $propretyData = $request->input('propertyData');

            CampaignProperty::create([
                'campaign_id' => $campaign->id,
                'property_id' => $propretyData['id'],
                'property_data' => $propretyData,
            ]);

            // Send emails if immediate
            if ($request->schedule['sendImmediately']) {
                $this->sendCampaignEmails($campaign);
                $campaign->update(['status' => 'sent']);
            } else {
                $campaign->update(['status' => 'scheduled']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->schedule['sendImmediately'] ?
                    'Campaign sent successfully!' :
                    'Campaign scheduled successfully!',
                'campaign_id' => $campaign->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Campaign send failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send campaign: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getRecipients(Request $request)
    {
        $query = Contact::where('user_id', $request->user()->id);

        switch ($request->contactSelection) {
            case 'all':
                return $query->get();

            case 'filtered':
                $filters = $request->contactFilters;

                if (!empty($filters['counties'])) {
                    $query->whereJsonContains('counties', $filters['counties']);
                }

                if (!empty($filters['dealTypes'])) {
                    $query->whereJsonContains('deal_type', $filters['dealTypes']);
                }

                if (!empty($filters['tags'])) {
                    $query->whereJsonContains('tags', $filters['tags']);
                }

                return $query->get();

            case 'selected':
                return Contact::whereIn('id', $request->selectedContacts)
                    ->where('user_id', $request->user()->id)
                    ->get();

            default:
                return collect();
        }
    }

    private function getPropertyData(array $propertyData)
    {
        $dbApiHelper = new \App\Helper\DBApiHelper();

        $request = new Request([
            'structure_type' => implode(',', $propertyData['propertyTypes'] ?? []),
            'list_price_min' => $propertyData['priceMin'] ?? 0,
            'list_price_max' => $propertyData['priceMax'] ?? 0,
            'total_finished_sqft_min' => $propertyData['sqftMin'] ?? 0,
            'total_finished_sqft_max' => $propertyData['sqftMax'] ?? 0,
            'counties' => implode(',', $propertyData['counties'] ?? []),
            'zip' => implode(',', $propertyData['zipCodes'] ?? []),
            'status' => implode(',', $propertyData['status'] ?? []),
            'closed_date_min' => $propertyData['closedDateMin'] ?? '',
            'distance_max' => $propertyData['distanceMax'] ?? 5,
            '_limit' => 50,
        ]);

        $response = $dbApiHelper->getPropertyListData($request);
        $data = json_decode($response->getContent(), true);

        return $data['data'] ?? [];
    }

    public function testCampaignEmail(Request $request, $id)
    {
        die('test');

        $campaign = Campaign::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $this->sendCampaignEmails($campaign);

        return response()->json(['success' => true]);
    }

    private function sendCampaignEmails(Campaign $campaign)
    {
        $recipients = $campaign->recipients()->where('status', 'pending')->get();

        if ($recipients->count() > 0) {
            $response = EmailMarketingHelper::sendMarketingEmail($campaign, $recipients);
        }
        return true;
    }

    public function campaignStats(Request $request, $id)
    {
        $campaign = Campaign::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $campignLog = CampaignLog::where('campaign_id', $id)->get();


        $errors = [];
        foreach ($campignLog as $key => $log) {
            $errors[$key]['message'] = $log->errors;
            $errors[$key]['recipients_count'] = count($log->recipients_ids);
        }

        // Get recipient stats
        $recipientStats = $campaign->recipients()
            ->selectRaw('
                COUNT(*) as total_recipients,
                SUM(CASE WHEN status = \'sent\' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = \'failed\' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = \'unsubscribed\' THEN 1 ELSE 0 END) as unsubscribed
            ')
            ->first();

        $openRate = EmailMarketingHelper::getCountByEvent('open', $request->user()->id, $id) ?? 0;
        $clickRate = EmailMarketingHelper::getCountByEvent('click', $request->user()->id, $id) ?? 0;

        $stats = [
            'total_recipients' => (int) $recipientStats->total_recipients ?? 0,
            'sent' => (int) $recipientStats->sent ?? 0,
            'failed' => (int) $recipientStats->failed ?? 0,
            'pending' => (int) $recipientStats->pending ?? 0,
            'unsubscribed' => (int) $recipientStats->unsubscribed ?? 0,
            'errors' => $errors,
            'open_rate' => ($recipientStats->total_recipients ?? 0) > 0 ? round(($openRate / $recipientStats->total_recipients) * 100, 1) : 0,
            'click_rate' => ($recipientStats->total_recipients ?? 0) > 0 ? round(($clickRate / $recipientStats->total_recipients) * 100, 1) : 0,
        ];

        return response()->json($stats);
    }

    public function getAllStates(Request $request, $campaignId)
    {
        $campaign = Campaign::where('id', $campaignId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Get campaign event statistics using the helper function
        $eventStats = EmailMarketingHelper::getCampaignEventStats($campaignId, $request->user()->id);

        return response()->json($eventStats);
    }

    public function getCampaignEmailPreview(Request $request)
    {
        $propertyInfo = $request->propertyInfo;
        $subject = $request->subject;
        $title = $request->headerText;
        $userDescription = $request->description;
        $userId = $request->user()->id;

        $html = EmailMarketingHelper::marketingMailPreviewHtml($propertyInfo, $subject, $title, $userDescription, $userId);

        return response()->json($html);
    }

    public function getCampaignRecipients(Request $request, $id)
    {
        $campaign = Campaign::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $recipients = $campaign->recipients()
            ->with('contact')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'recipients' => $recipients,
            'campaign' => $campaign
        ]);
    }

}
