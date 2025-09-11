<?php

namespace App\Http\Controllers;

use App\Models\MyBuyerList;
use App\Models\MyBuyerListBuyer;
use App\Models\Contact;
use App\Models\ContactTagOwnership;
use App\Services\DirectSkipService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\SkipTraceResult;


class BuyerSkipTraceController extends Controller
{
    protected $directSkipService;

    public function __construct(DirectSkipService $directSkipService)
    {
        $this->directSkipService = $directSkipService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $listId = $request->query('list_id');
        $lastDays = (int) $request->query('lastDays', 30); // Add this line
        $from = now()->subDays($lastDays);
        // 1) Load lists + each list's buyers with their own skipTraceResult
        $buyerLists = MyBuyerList::where('user_id', $user->id)
        ->with([
            'buyers' => function ($query) use ($from) { // Add $from parameter
                $query->with('skipTraceResult')
                      ->where(function($q) use ($from) {
                          // Filter by either created_at OR last_traced_at
                          $q->where('created_at', '>=', $from)
                            ->orWhere('last_traced_at', '>=', $from);
                      })
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->get();
            
        // 2) Build a set of all investor identifiers across these lists
        $allInvestorIds = collect($buyerLists)
            ->flatMap(fn($list) => $list->buyers->pluck('investor_identifier'))
            ->filter()
            ->unique()
            ->values();

        // 3) Fetch community (global) results for those investor identifiers (latest per investor)
        //    NOTE: adjust model/column names if different in your codebase.
        $communityResults = SkipTraceResult::whereIn('investor_identifier', $allInvestorIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('investor_identifier')   // keep the latest per investor
            ->keyBy('investor_identifier');

        // Helper to decide if a result is "successful"
        $isSuccessful = function ($r) {
            if (!$r)
                return false;
            $hasEmail = !empty($r->emails) && is_array($r->emails) && count($r->emails) > 0;
            $hasPhone = !empty($r->phone_numbers) && is_array($r->phone_numbers) && count($r->phone_numbers) > 0;
            return $hasEmail || $hasPhone;
        };

        // 4) Map lists -> buyers, merging own result with community result
        $buyerLists = $buyerLists->map(function ($list) use ($communityResults, $isSuccessful) {

            $buyers = $list->buyers;
            $successfulCount = 0;
            $failedCount = 0;
            $totalEmailsReturned = 0;
            $totalPhonesReturned = 0;

            $buyersOut = $buyers->map(function ($buyer) use ($communityResults, $isSuccessful, &$successfulCount, &$failedCount, &$totalEmailsReturned, &$totalPhonesReturned) {
                // Prefer the list's own result; fall back to community result
                $own = $buyer->skipTraceResult;
                $comm = $communityResults->get($buyer->investor_identifier);
                $data = $own ?? $comm;  // <- this makes contact info "community"

                // Compute counters from whichever data we used
                if ($data) {
                    if ($isSuccessful($data)) {
                        $successfulCount++;
                        $totalEmailsReturned += !empty($data->emails) ? count($data->emails) : 0;
                        $totalPhonesReturned += !empty($data->phone_numbers) ? count($data->phone_numbers) : 0;
                    } else {
                        $failedCount++;
                    }
                }

                return [
                    'id' => $buyer->id,
                    'investor_identifier' => $buyer->investor_identifier,
                    // Property address (for display)
                    'mrp_fullstreet' => $buyer->mrp_fullstreet,
                    'mrp_city' => $buyer->mrp_city,
                    'mrp_state' => $buyer->mrp_state,
                    'mrp_zip' => $buyer->mrp_zip,
                    'mrp_sales_price' => $buyer->mrp_sales_price,
                    'mrp_purchase' => $buyer->mrp_purchase,
                    // MAILING (for reference)
                    'MailingFullStreetAddress' => $buyer->MailingFullStreetAddress,
                    'MailingCity' => $buyer->MailingCity,
                    'MailingState' => $buyer->MailingState,
                    'MailingZIP5' => $buyer->MailingZIP5,

                    // Use merged skip trace data (own OR community)
                    'first_name' => $data->first_name ?? null,
                    'last_name' => $data->last_name ?? null,
                    'age' => $data->age ?? null,
                    'deceased' => $data->deceased ?? false,
                    'phone_numbers' => $data->phone_numbers ?? [],
                    'phone_type' => $data->phone_type ?? [],
                    'emails' => $data->emails ?? [],
                    'street' => $data->street ?? null,
                    'city' => $data->city ?? null,
                    'state' => $data->state ?? null,
                    'zip' => $data->zip ?? null,
                    'connected_people' => $data->connected_people ?? [],
                    // Consider buyer "skip_traced" if either own or community data exists
                    'skip_traced' => (bool) $data,
                ];
            });

            // For the top-level list counts, use the merged data above
            return [
                'id' => $list->id,
                'name' => $list->name,
                'buyer_count' => $buyersOut->count(),
                'skip_traced_count' => $buyersOut->where('skip_traced', true)->count(),
                'successful_count' => $successfulCount,
                'failed_count' => $failedCount,
                'total_emails_returned' => $totalEmailsReturned,
                'total_phones_returned' => $totalPhonesReturned,
                'created_at' => $list->created_at->format('M j, Y'),
                'buyers' => $buyersOut,
            ];
        });

        // Optional: a global community metric (keep if you need it)
        $communitySkipTraces = SkipTraceResult::count();

        return Inertia::render('buyers/SkipTracing', [
            'buyerLists' => $buyerLists,
            'selectedListId' => $listId,
            'user' => $user,
            'communitySkipTraces' => $communitySkipTraces,
        ]);
    }


    public function skipTraceList(Request $request, $listId)
    {
        try {
            $request->validate([
                'list_id' => 'required|exists:my_buyer_lists,id'
            ]);
            $scope  = $request->input('scope', 'missing'); // 'missing' (default) or 'all'
$userId = $request->user()->id;

            $list = MyBuyerList::where('id', $listId)
                ->where('user_id', $request->user()->id)
                ->with('buyers')
                ->firstOrFail();

            Log::info('Starting skip trace for list', [
                'list_id' => $listId,
                'buyer_count' => $list->buyers->count()
            ]);

            $results = [];
            $contactsCreated = 0;
            $recordsSubmitted = 0;
            $apiCallsMade = 0;
            $successfulReturns = 0;
            $failedReturns = 0;
            $totalEmailsReturned = 0;
            $totalPhonesReturned = 0;
            $totalContactInfoReturned = 0;
            $billableSuccess = 0; // how many API runs returned phones/emails -> bill these


            foreach ($list->buyers as $buyer) {
                Log::info('Processing buyer for skip trace', [
                    'buyer_id' => $buyer->id,
                    'investor_identifier' => $buyer->investor_identifier,
                    'mailing_address' => $buyer->MailingFullStreetAddress ?? 'NULL',
                    'mailing_city' => $buyer->MailingCity ?? 'NULL',
                    'mailing_state' => $buyer->MailingState ?? 'NULL'
                ]);
            
                $recordsSubmitted++;
            
                // 1) Check existing data (community/cached)
                $existingData = $this->directSkipService->checkExistingSkipTraceData($buyer->toArray());
                $existingHasContact = $existingData ? $this->isSuccessfulSkipTrace($existingData) : false;
            
                // 2) If scope='missing' and contact already exists => attach community (no API, no bill)
                if ($scope === 'missing' && $existingHasContact) {
            
                    // Ensure this row points at the community result for display
                    if (!empty($existingData['skip_trace_result_id'])) {
                        $buyer->skip_trace_result_id = $existingData['skip_trace_result_id'];
                    }
            
                    // mark as community view (not billable)
                    $buyer->last_traced_by_user_id = null;
                    $buyer->last_traced_at         = null;
                    $buyer->last_trace_success     = null;
                    $buyer->last_trace_source      = 'community';
                    $buyer->save();
            
                    // success for UI stats only (not billed)
                    $emailsCount = !empty($existingData['emails']) ? count($existingData['emails']) : 0;
                    $phonesCount = !empty($existingData['phone_numbers']) ? count($existingData['phone_numbers']) : 0;
            
                    $successfulReturns++;
                    $totalEmailsReturned     += $emailsCount;
                    $totalPhonesReturned     += $phonesCount;
                    $totalContactInfoReturned += ($emailsCount + $phonesCount);
            
                    // create/update contact (free attach)
                    $contactCreated = $this->createContactRecord($buyer, $userId, $list->name);
                    if ($contactCreated) $contactsCreated++;
            
                    $results[] = [
                        'buyer_id'        => $buyer->id,
                        'success'         => true,
                        'data_source'     => 'community',
                        'contact_info'    => $existingData,
                        'contact_created' => (bool)$contactCreated,
                    ];
            
                    continue; // IMPORTANT: do not call API for this buyer
                }
            
                // 3) Otherwise (scope='all' OR 'missing' without contact) => call API (billable if success)
                $skipTraceData = $this->directSkipService->searchContact($buyer->toArray());
                $apiCallsMade++;
                $isSuccessful  = $this->isSuccessfulSkipTrace($skipTraceData);
            
                // Store result id if present
                if (!empty($skipTraceData['skip_trace_result_id'])) {
                    $buyer->skip_trace_result_id = $skipTraceData['skip_trace_result_id'];
                }
            
                // audit columns for API run
                $buyer->last_traced_by_user_id = $userId;
                $buyer->last_traced_at         = now();
                $buyer->last_trace_success     = $isSuccessful;
                $buyer->last_trace_source      = 'api';
                $buyer->save();
            
                if ($isSuccessful) {
                    $billableSuccess++; // <-- this is what you charge
                    $successfulReturns++;
            
                    $emailsCount = !empty($skipTraceData['emails']) ? count($skipTraceData['emails']) : 0;
                    $phonesCount = !empty($skipTraceData['phone_numbers']) ? count($skipTraceData['phone_numbers']) : 0;
            
                    $totalEmailsReturned     += $emailsCount;
                    $totalPhonesReturned     += $phonesCount;
                    $totalContactInfoReturned += ($emailsCount + $phonesCount);
            
                    Log::info('Skip trace successful - API', [
                        'buyer_id' => $buyer->id,
                        'emails_count' => $emailsCount,
                        'phones_count' => $phonesCount
                    ]);
                } else {
                    $failedReturns++;
                    Log::info('Skip trace failed - API', ['buyer_id' => $buyer->id]);
                }
            
                // create/update contact for API result
                $contactCreated = $this->createContactRecord($buyer, $userId, $list->name);
                if ($contactCreated) $contactsCreated++;
            
                $results[] = [
                    'buyer_id'        => $buyer->id,
                    'success'         => $isSuccessful,
                    'data_source'     => 'api',
                    'contact_info'    => $skipTraceData,
                    'contact_created' => (bool)$contactCreated,
                ];
            }
            
            Log::info('Skip trace usage summary', [
                'user_id' => $request->user()->id,
                'list_id' => $listId,
                'list_name' => $list->name,
                'records_submitted' => $recordsSubmitted,
                'api_calls_made' => $apiCallsMade,
                'cached_data_used' => $recordsSubmitted - $apiCallsMade,
                'successful_returns' => $successfulReturns,
                'failed_returns' => $failedReturns,
                'total_emails_returned' => $totalEmailsReturned,
                'total_phones_returned' => $totalPhonesReturned,
                'total_contact_info_returned' => $totalContactInfoReturned,
                'contacts_created' => $contactsCreated,
                'success_rate' => $recordsSubmitted > 0 ? round(($successfulReturns / $recordsSubmitted) * 100, 2) : 0
            ]);
            Log::info('Skip trace completed', [
                'list_id' => $listId,
                'total_buyers' => count($results),
                'successful_traces' => count(array_filter($results, fn($r) => $r['success'])),
                'contacts_created' => $contactsCreated
            ]);

            return response()->json([
                'success' => true,
                'results' => $results,
                'contacts_created' => $contactsCreated,
                'billable_success'  => $billableSuccess,
                'message' => "Skip tracing completed for " . count($results) . " buyers. {$contactsCreated} new contacts created."
            ]);

        } catch (\Exception $e) {
            Log::error('Skip trace error', [
                'list_id' => $listId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Skip tracing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createContactRecord($buyer, $userId, $listName = null)
    {
        try {
            // Get skip trace data from relationship
            $skipTraceData = $buyer->skipTraceResult;
            if (!$skipTraceData) {
                Log::warning('No skip trace data found for buyer', [
                    'buyer_id' => $buyer->id,
                    'investor_identifier' => $buyer->investor_identifier
                ]);
                return false;
            }

            Log::info('Processing contact creation for buyer', [
                'buyer_id' => $buyer->id,
                'investor_identifier' => $buyer->investor_identifier,
                'has_emails' => !empty($skipTraceData->emails),
                'has_phones' => !empty($skipTraceData->phone_numbers),
                'email_count' => count($skipTraceData->emails ?? []),
                'phone_count' => count($skipTraceData->phone_numbers ?? [])
            ]);

            $ownerName = trim($skipTraceData->first_name . ' ' . $skipTraceData->last_name);
            $ownerEmail = $skipTraceData->emails[0] ?? '';
            $ownerPhone = $skipTraceData->phone_numbers[0] ?? '';

            // Check if contact already exists by email or name (for this user)
            $existingContact = null;
            if (!empty($ownerEmail)) {
                $existingContact = Contact::where('email', $ownerEmail)
                    ->where('user_id', $userId)
                    ->first();
            }

            if (!$existingContact && !empty($ownerName)) {
                $existingContact = Contact::where('name', $ownerName)
                    ->where('user_id', $userId)
                    ->first();
            }

            // If contact exists for this user, just update tags
            if ($existingContact) {
                Log::info('Contact already exists for this user, updating tags', [
                    'contact_id' => $existingContact->id,
                    'buyer_id' => $buyer->id,
                    'investor_identifier' => $buyer->investor_identifier
                ]);

                $currentTags = $existingContact->tags ?? [];
                $newTags = ['Skip-Traced'];

                // Add list name tag if provided
                if ($listName && !empty($listName) && $listName !== '-- None --') {
                    $newTags[] = $listName;
                }

                // Merge tags and remove duplicates
                $updatedTags = array_unique(array_merge($currentTags, $newTags));

                $existingContact->update([
                    'tags' => $updatedTags
                ]);

                // Track ownership of tags
                $this->trackTagOwnershipForContact($existingContact, $updatedTags, $userId);

                return true;
            }

            // Create new contact for ALL skip trace results (with or without contact info)
            $tags = ['Skip-Traced'];

            // Add list name if provided
            if ($listName && !empty($listName) && $listName !== '-- None --') {
                $tags[] = $listName;
            }

            $contactData = [
                'user_id' => $userId,
                'email' => $ownerEmail,
                'first_name' => $skipTraceData->first_name,
                'last_name' => $skipTraceData->last_name,
                'phone' => $ownerPhone,
                'name' => $ownerName,
                'tags' => array_values($tags),
                'email_type' => 'regular'
            ];

            Log::info('Creating new contact', [
                'contact_data' => $contactData,
                'buyer_id' => $buyer->id,
                'investor_identifier' => $buyer->investor_identifier,
                'has_contact_info' => !empty($ownerEmail) || !empty($ownerPhone)
            ]);

            $contact = Contact::create($contactData);

            // Track ownership of tags
            $this->trackTagOwnershipForContact($contact, $tags, $userId);

            Log::info('Contact created successfully', [
                'contact_id' => $contact->id,
                'contact_name' => $contact->name,
                'contact_email' => $contact->email,
                'contact_phone' => $contact->phone,
                'buyer_id' => $buyer->id,
                'investor_identifier' => $buyer->investor_identifier,
                'has_contact_info' => !empty($ownerEmail) || !empty($ownerPhone)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error creating contact', [
                'buyer_id' => $buyer->id,
                'investor_identifier' => $buyer->investor_identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    public function searchBuyersWithSkipTrace(Request $request)
    {
        try {
            // First get the buyer search results from DBApiHelper (external API)
            $dbApiHelper = new \App\Helper\DBApiHelper();
            $buyerResultsResponse = $dbApiHelper->searchBuyers($request);

            // Extract the data from the JsonResponse
            $buyerResults = $buyerResultsResponse->getData(true);

            // Check if we have data
            if (!isset($buyerResults['data']) || empty($buyerResults['data'])) {
                return $buyerResultsResponse;
            }

            // Get all investor identifiers from the results
            $investorIdentifiers = collect($buyerResults['data'])->pluck('investor_identifier')->toArray();

            // Query YOUR DATABASE for skip trace data
            $skipTraceData = \App\Models\SkipTraceResult::whereIn('investor_identifier', $investorIdentifiers)->get();

            // Get all emails from skip trace results to match with contacts
            $skipTraceEmails = [];
            foreach ($skipTraceData as $skipTrace) {
                if (!empty($skipTrace->emails)) {
                    foreach ($skipTrace->emails as $email) {
                        $skipTraceEmails[] = $email;
                    }
                }
            }

            // Query YOUR DATABASE for contact data (matching by email from skip trace)
            $contactData = Contact::whereIn('email', $skipTraceEmails)->get();

            // Debug logging
            \Log::info('Contact matching debug', [
                'investor_identifiers' => $investorIdentifiers,
                'skip_trace_emails' => $skipTraceEmails,
                'contacts_found' => $contactData->count(),
                'contact_emails' => $contactData->pluck('email')->toArray()
            ]);

            // Create lookup maps
            $skipTraceMap = [];
            foreach ($skipTraceData as $result) {
                $skipTraceMap[$result->investor_identifier] = $result;
            }

            $contactMap = [];
            foreach ($contactData as $contact) {
                $contactMap[$contact->email] = $contact;
            }

            // Enrich buyer results with skip trace data and contact tags
            $enrichedResults = [];
            foreach ($buyerResults['data'] as $buyer) {
                $skipTraceInfo = $skipTraceMap[$buyer['investor_identifier']] ?? null;

                $enrichedBuyer = $buyer;
                if ($skipTraceInfo) {
                    // This buyer has been skip traced
                    $enrichedBuyer['skip_traced'] = true;
                    $enrichedBuyer['owner_name'] = trim($skipTraceInfo->first_name . ' ' . $skipTraceInfo->last_name);
                    $enrichedBuyer['owner_age'] = $skipTraceInfo->age;
                    $enrichedBuyer['owner_phone'] = $skipTraceInfo->phone_numbers[0] ?? null;
                    $enrichedBuyer['owner_email'] = $skipTraceInfo->emails[0] ?? null;
                    $enrichedBuyer['owner_deceased'] = $skipTraceInfo->deceased;

                    // NEW: Determine if skip trace found contact info
                    $hasContactInfo = !empty($skipTraceInfo->phone_numbers) || !empty($skipTraceInfo->emails);
                    $enrichedBuyer['skip_trace_has_contact_info'] = $hasContactInfo;

                    // Get the list name for this specific buyer
                    $listName = $this->getBuyerListName($buyer['investor_identifier'], $request->user()->id);

                    $contactInfo = $this->createOrUpdateContactFromSkipTrace($skipTraceInfo, $listName, $request->user()->id);

                    // Add contact tags if found
                    // Add contact tags if found
                    if ($contactInfo) {
                        // Get tags directly from the contact (which now only contains user's own tags)
                        $tags = $contactInfo->tags ?? [];

                        // Use the proper filtering methods that check user ownership
                        $enrichedBuyer['public_tags'] = $this->getPublicTags($tags);
                        $enrichedBuyer['private_tags'] = $this->getPrivateTagsForUser($tags, $contactInfo->id, $request->user()->id);
                        $enrichedBuyer['contact_id'] = $contactInfo->id;
                    } else {
                        $enrichedBuyer['public_tags'] = [];
                        $enrichedBuyer['private_tags'] = [];
                        $enrichedBuyer['contact_id'] = null;
                    }
                }

                $enrichedResults[] = $enrichedBuyer;
            }

            // Return in the correct format that frontend expects
            return response()->json([
                'status' => 1,
                'data' => $enrichedResults,
                'message' => 'Success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in searchBuyersWithSkipTrace: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
    // Add this method to your DBApiHelper.php (after line 504 or at the end of the class)
    private function createOrUpdateContactFromSkipTrace($skipTraceInfo, $listName = null, $userId = null)
    {
        $ownerName = trim($skipTraceInfo->first_name . ' ' . $skipTraceInfo->last_name);
        $ownerEmail = $skipTraceInfo->emails[0] ?? '';
        $ownerPhone = $skipTraceInfo->phone_numbers[0] ?? '';

        // Check if contact already exists by email or name (for this user)
        $existingContact = null;
        if (!empty($ownerEmail)) {
            $existingContact = Contact::where('email', $ownerEmail)
                ->where('user_id', $userId)
                ->first();
        }

        if (!$existingContact && !empty($ownerName)) {
            $existingContact = Contact::where('name', $ownerName)
                ->where('user_id', $userId)
                ->first();
        }
        if (!$existingContact) {
            // Check if any other user has already created a contact for this buyer
            $otherUserContact = Contact::where('email', $ownerEmail)
                ->where('user_id', '!=', $userId)
                ->first();

            if ($otherUserContact) {
                \Log::info('Contact already exists for another user, adding current user tags', [
                    'existing_contact_id' => $otherUserContact->id,
                    'existing_user_id' => $otherUserContact->user_id,
                    'current_user_id' => $userId,
                    'email' => $ownerEmail
                ]);

                // Add current user's tags to the existing contact
                $currentTags = $otherUserContact->tags ?? [];
                $newTags = ['Skip-Traced'];

                // Add list name if provided
                if ($listName && !empty($listName) && $listName !== '-- None --') {
                    $listNameArray = array_map('trim', explode(',', $listName));
                    foreach ($listNameArray as $singleListName) {
                        if (!empty($singleListName) && $singleListName !== '-- None --' && !in_array($singleListName, $currentTags)) {
                            $newTags[] = $singleListName;
                        }
                    }
                }

                // Merge tags and update contact
                $updatedTags = array_unique(array_merge($currentTags, $newTags));
                $otherUserContact->update(['tags' => array_values($updatedTags)]);

                // Track ownership of new tags for current user
                foreach ($newTags as $tag) {
                    if ($tag !== 'Skip-Traced') {
                        ContactTagOwnership::updateOrCreate(
                            [
                                'contact_id' => $otherUserContact->id,
                                'tag_name' => $tag,
                                'user_id' => $userId
                            ],
                            [
                                'is_public' => false
                            ]
                        );
                    } else {
                        ContactTagOwnership::updateOrCreate(
                            [
                                'contact_id' => $otherUserContact->id,
                                'tag_name' => $tag,
                                'user_id' => $userId
                            ],
                            [
                                'is_public' => true
                            ]
                        );
                    }
                }

                return $otherUserContact;
            }
        }

        if ($existingContact) {
            $updateData = [];
            if (!empty($ownerPhone))
                $updateData['phone'] = $ownerPhone;
            if (!empty($ownerEmail))
                $updateData['email'] = $ownerEmail;
            if (!empty($updateData))
                $existingContact->update($updateData);

            // Build a clean tag set for THIS user only
            $newTags = ['Skip-Traced'];

            if ($listName && $listName !== '-- None --') {
                foreach (array_map('trim', explode(',', $listName)) as $single) {
                    if ($single !== '')
                        $newTags[] = $single;
                }
            }

            // REPLACE (don’t merge) to avoid keeping other users’ tags from the past
            $existingContact->update(['tags' => array_values(array_unique($newTags))]);

            // Refresh ownership rows for THIS user only
            $this->trackTagOwnershipForContact($existingContact, $newTags, $userId);

            return $existingContact;

        } else {
            // Create new contact, even if phone/email are empty
            $tags = ['Skip-Traced'];

            // Add list name if provided
            if ($listName && !empty($listName) && $listName !== '-- None --') {
                // Split multiple list names by comma
                $listNameArray = array_map('trim', explode(',', $listName));
                foreach ($listNameArray as $singleListName) {
                    if (!empty($singleListName) && $singleListName !== '-- None --') {
                        $tags[] = $singleListName;
                    }
                }
            }

            // Create the contact first
            $newContact = Contact::create([
                'name' => $ownerName,
                'first_name' => $skipTraceInfo->first_name,
                'last_name' => $skipTraceInfo->last_name,
                'email' => $ownerEmail,
                'phone' => $ownerPhone,
                'tags' => array_values($tags),
                'user_id' => $userId,
                'email_type' => 'regular'
            ]);

            // Track ownership of private tags
            if ($listName && !empty($listName) && $listName !== '-- None --') {
                $listNameArray = array_map('trim', explode(',', $listName));
                foreach ($listNameArray as $singleListName) {
                    if (!empty($singleListName) && $singleListName !== '-- None --') {
                        \Log::info('Creating contact tag ownership for list name', [
                            'contact_id' => $newContact->id,
                            'tag_name' => $singleListName,
                            'user_id' => $userId
                        ]);

                        ContactTagOwnership::create([
                            'contact_id' => $newContact->id,
                            'tag_name' => $singleListName,
                            'user_id' => $userId,
                            'is_public' => false
                        ]);
                    }
                }
            } else {
                \Log::info('No list name provided for contact', [
                    'contact_id' => $newContact->id,
                    'list_name' => $listName,
                    'user_id' => $userId
                ]);
            }

            // Always track ownership of Skip-Traced tag for new contacts
            \Log::info('Creating contact tag ownership for Skip-Traced', [
                'contact_id' => $newContact->id,
                'tag_name' => 'Skip-Traced',
                'user_id' => $userId
            ]);

            ContactTagOwnership::create([
                'contact_id' => $newContact->id,
                'tag_name' => 'Skip-Traced',
                'user_id' => $userId,
                'is_public' => true
            ]);

            return $newContact;
        }
    }
    // BuyerSkipTraceController.php
    private function getBuyerListName(string $investorIdentifier, int $userId)
    {
        $buyerListBuyers = MyBuyerListBuyer::query()
            ->where('investor_identifier', $investorIdentifier)
            ->whereHas('list', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['list:id,name,user_id'])
            ->get();

        $listNames = $buyerListBuyers->pluck('list.name')->filter()->unique()->values()->all();

        \Log::info('getBuyerListName (scoped to user)', [
            'investor_identifier' => $investorIdentifier,
            'user_id' => $userId,
            'list_names' => $listNames,
        ]);

        return !empty($listNames) ? implode(', ', $listNames) : null;
    }

    private function getPublicTags($tags)
    {
        // Only "Skip-Traced" is public
        return array_filter($tags, function ($tag) {
            return $tag === 'Skip-Traced';
        });
    }

    private function getPrivateTagsForUser($tags, $contactId, $userId)
    {
        // Get all tags except "Skip-Traced"
        $privateTags = array_filter($tags, function ($tag) {
            return $tag !== 'Skip-Traced';
        });

        // Check ownership in the database - ONLY for this specific contact and user
        $ownedTags = ContactTagOwnership::where('contact_id', $contactId)
            ->where('user_id', $userId)
            ->where('is_public', false)
            ->pluck('tag_name')
            ->toArray();

        // Add debug logging to see what's happening
        \Log::info('getPrivateTagsForUser debug', [
            'contact_id' => $contactId,
            'user_id' => $userId,
            'all_tags' => $tags,
            'private_tags' => $privateTags,
            'owned_tags' => $ownedTags,
            'result' => array_values(array_intersect($privateTags, $ownedTags))
        ]);

        // Only return tags that are explicitly owned by this user for this specific contact
        return array_values(array_intersect($privateTags, $ownedTags));
    }
    // Add this helper method to track tag ownership
    private function trackTagOwnershipForContact($contact, $tags, $userId)
    {
        // Remove existing ownership entries for this contact and user
        ContactTagOwnership::where('contact_id', $contact->id)
            ->where('user_id', $userId)
            ->delete();

        // Add ownership for each tag
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                ContactTagOwnership::create([
                    'contact_id' => $contact->id,
                    'tag_name' => $tag,
                    'user_id' => $userId,
                    'is_public' => ($tag === 'Skip-Traced') // Only Skip-Traced is public
                ]);
            }
        }
    }
    // Add this method at the very end of the BuyerSkipTraceController class
    private function isSuccessfulSkipTrace($skipTraceData)
    {
        $hasEmail = !empty($skipTraceData['emails']) && count($skipTraceData['emails']) > 0;
        $hasPhone = !empty($skipTraceData['phone_numbers']) && count($skipTraceData['phone_numbers']) > 0;

        return $hasEmail || $hasPhone;
    }
    public function stats()
    {
        $buyerLists = MyBuyerList::with(['buyers.skipTraceResult'])
            ->where('user_id', auth()->id())
            ->get()
            ->map(function ($list) {
                $buyers = $list->buyers;
                $skipTracedBuyers = $buyers->whereNotNull('skip_trace_result_id');

                $successfulCount = 0;
                $failedCount = 0;
                $totalEmailsReturned = 0;
                $totalPhonesReturned = 0;

                foreach ($skipTracedBuyers as $buyer) {
                    $skipTraceData = $buyer->skipTraceResult;
                    if ($skipTraceData) {
                        $hasEmail = !empty($skipTraceData->emails) && count($skipTraceData->emails) > 0;
                        $hasPhone = !empty($skipTraceData->phone_numbers) && count($skipTraceData->phone_numbers) > 0;

                        if ($hasEmail || $hasPhone) {
                            $successfulCount++;
                            $totalEmailsReturned += !empty($skipTraceData->emails) ? count($skipTraceData->emails) : 0;
                            $totalPhonesReturned += !empty($skipTraceData->phone_numbers) ? count($skipTraceData->phone_numbers) : 0;
                        } else {
                            $failedCount++;
                        }
                    }
                }

                return [
                    'id' => $list->id,
                    'name' => $list->name,
                    'buyer_count' => $list->buyers->count(),
                    'skip_traced_count' => $list->buyers->whereNotNull('skip_trace_result_id')->count(),
                    'successful_count' => $successfulCount,
                    'failed_count' => $failedCount,
                    'total_emails_returned' => $totalEmailsReturned,
                    'total_phones_returned' => $totalPhonesReturned,
                    'created_at' => $list->created_at->format('M j, Y'),
                    'buyers' => $list->buyers
                ];
            });

        return Inertia::render('buyers/SkipTrace', [
            'buyerLists' => $buyerLists,
            'user' => auth()->user()
        ]);
    }
    // 1) COMMUNITY CONTACT LOOKUP (no user_id filter)
    private function getCommunityContactForBuyer(?string $email, ?string $name)
    {
        if (!empty($email)) {
            $byEmail = Contact::where('email', $email)->first();
            if ($byEmail)
                return $byEmail;
        }
        if (!empty($name)) {
            $byName = Contact::where('name', $name)->first();
            if ($byName)
                return $byName;
        }
        return null;
    }

    // 2) HYDRATE ONE BUYER with community contact + private tags
    private function hydrateBuyerWithContact($buyer, int $userId)
    {
        // Adjust these two lines to match your buyer row fields
        $ownerName = trim(($buyer->first_name ?? '') . ' ' . ($buyer->last_name ?? ''));
        $ownerEmail = $buyer->owner_email ?? null; // if you don't have owner_email, keep null

        $contact = $this->getCommunityContactForBuyer($ownerEmail, $ownerName);

        // Community contact info visible to everyone
        $buyer->skip_traced = (bool) $contact;
        $buyer->owner_name = $contact?->name;

        // Normalize to arrays (your Vue expects arrays)
        $buyer->phones = [];
        $buyer->emails = [];
        if ($contact) {
            if (!empty($contact->phone))
                $buyer->phones[] = $contact->phone;
            if (!empty($contact->email))
                $buyer->emails[] = $contact->email;
        }
        $buyer->skip_trace_has_contact_info = !empty($buyer->phones) || !empty($buyer->emails);

        // Tags: public (“Skip-Traced”) + ONLY this user’s private tags
        if ($contact) {
            $allTags = $contact->tags ?? [];
            $public = $this->getPublicTags($allTags); // you already have this
            $private = $this->getPrivateTagsForUser($allTags, $contact->id, $userId); // you already have this
            $buyer->tags = array_values(array_unique(array_merge($public, $private)));
        } else {
            $buyer->tags = [];
        }

        // Optional: if your Vue uses phone_numbers instead of phones
        $buyer->phone_numbers = $buyer->phones;

        return $buyer;
    }

}