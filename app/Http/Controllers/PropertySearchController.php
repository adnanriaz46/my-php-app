<?php

namespace App\Http\Controllers;

use App\Helper\CommonHelper;
use App\Helper\PropertyHelper;
use App\Helper\ValueFormat;
use App\Http\Requests\SavedSearchRequest;
use App\Models\AddressRequest;
use App\Models\AskQuestion;
use App\Models\BuyerFinancing;
use App\Models\CountyState;
use App\Models\InstantOffer;
use App\Models\MyPropertyList;
use App\Models\MyUnlistedList;
use App\Models\PropertyFilterHistory;
use App\Models\PropertyViewHistory;
use App\Models\RequestAShowing;
use App\Models\SavedSearch;
use App\Models\SuppressProperty;
use App\Models\UnlistedViewHistory;
use App\Models\User;
use App\Models\WholesaleProperty;
use App\Services\EmailService;
use Aws\History;
use Dotenv\Parser\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Intervention\Image\Format;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use PharIo\Manifest\Email;
use function Pest\Laravel\json;

class PropertySearchController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('property-search/PropertySearch', [
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function saveSearch(SavedSearchRequest $request)
    {

        $userId = $request->user()->id;
        $customErrorList = [];

        $savedAsNew = $request->input('savedSearchAsNew');
        $savedSearchId = $request->input('savedSearchId');
        $savedSearchName = $request->input('savedSearchName');


        if (!$userId) {
            $customErrorList['user_id'] = 'Your request was unauthorized, please reload';
        }
        if (!$savedAsNew && !$savedSearchId) {
            $customErrorList['savedSearchId'] = 'You should provide an existing saved list for update.';
        }

        if ($savedAsNew) {
            $exists = SavedSearch::where('user_id', $userId)
                ->where('saved_search_name', $savedSearchName)
                ->exists(); // fixed method typo

            if ($exists) {
                $customErrorList['savedSearchName'] = 'You already used this name. Please provide another.';
            }
        } else {
            $exists = SavedSearch::where('user_id', $userId)
                ->where('saved_search_name', $savedSearchName)
                ->where('id', '!=', $savedSearchId)
                ->exists(); // fixed method typo

            if ($exists) {
                $customErrorList['savedSearchName'] = 'You already used this name. Please provide another.';
            }
        }

        if (!empty($customErrorList)) {
            return back()->withErrors($customErrorList);
        }
        $message = '';
        try {
            $data = $request->validated();

            unset($data['savedSearchName'], $data['savedSearchId'], $data['savedSearchAsNew']);

            $data['user_id'] = $userId;
            $data['saved_search_name'] = $savedSearchName;
            $data['property_id'] = $data['id'] ?? null;
            $data['saved_search_type'] = 'Custom';
            $data['proximity_object'] = json_encode($request->input('proximity_object'));

            if ($savedAsNew) {
                $savedSearch = SavedSearch::create($data);
                $savedSearchId = $savedSearch->id;
                $message = 'Search saved successfully.';
            } else {
                $search = SavedSearch::where('id', $savedSearchId)
                    ->where('user_id', $userId)
                    ->first();
                if (!$search) {
                    return back()->withErrors(['savedSearchId' => 'Saved search not found.'])->withInput();
                }
                $search->update($data);
                $message = 'Search updated successfully';
            }
        } catch (\Exception $exception) {
            $customErrorList['exception'] = 'Something went wrong! ' . $exception->getMessage();
        }

        if (!empty($customErrorList)) {
            return back()->withErrors($customErrorList);
        }

        return redirect()->back()->with(['success' => $message, 'responseId' => $savedSearchId]);
    }


    public function getSavedSearchList(Request $request)
    {
        $userId = $request->user()->id;

        if ($userId) {
            //            select(['id', 'saved_search_name', 'saved_search_type'])->
            $data = SavedSearch::where('user_id', $userId)->get();
        }

        return response()->json(['data' => $data]);
    }

    public function setSaveSearchDefault(Request $request)
    {
        $userId = $request->user()->id;
        $savedSearchId = $request->input('id');

        // Ensure the target search belongs to the user
        $search = SavedSearch::where('id', $savedSearchId)
            ->where('user_id', $userId)
            ->first();

        if (!$search) {
            return response()->json(['error' => 'Saved search not found.']);
        }

        // Reset all defaults, then set the selected one
        SavedSearch::where('user_id', $userId)->update(['default' => false]);
        $search->update(['default' => true]);

        return response()->json(['success' => 'Saved search default updated']);
    }

    public function getSavedSearchById(Request $request, $id)
    {
        $userId = $request->user()->id;
        if ($userId) {
            $data = SavedSearch::where('user_id', $userId)->where('id', $id)->first();
        }
        return response()->json(['data' => $data]);
    }

    public function deleteSavedSearchById(Request $request)
    {
        $userId = $request->user()->id;
        $id = $request->input('id');

        if ($userId) {
            SavedSearch::where('user_id', $userId)->where('id', $id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Saved Search has been removed']);
    }

    public function propertyHide(Request $request)
    {

        $request->validate([
            'property_ids' => ['required', 'array', 'min:1'],
            'property_ids.*' => ['integer', 'distinct'],
        ]);

        $propertyIds = $request->input('property_ids');
        $userId = $request->user()->id;

        $data = collect($propertyIds)->map(function ($id) use ($userId) {
            return [
                'property_id' => $id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        $existing = SuppressProperty::where('user_id', $userId)
            ->whereIn('property_id', $propertyIds)
            ->pluck('property_id')
            ->toArray();

        $filtered = array_filter($data, fn($item) => !in_array($item['property_id'], $existing));

        if (!empty($filtered)) {
            SuppressProperty::insert($filtered);
        }

        return response()->json(['success' => 'Properties have been hidden']);
    }

    public function getHiddenProperties(Request $request)
    {
        $userId = $request->user()->id;

        $data = SuppressProperty::where('user_id', $userId)->pluck('property_id')
            ->toArray();
        return response()->json(['data' => $data, 'success' => 'ok']);
    }

    public function getMyPropertyList(Request $request)
    {
        $res = MyPropertyList::where('user_id', $request->user()->id)->get();
        return $res;
    }

    public function createMyPropertyList(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:20|unique:my_property_lists,name',
            'selectionIds' => ['required', 'array'],
            'selectionIds.*' => ['integer'],
        ]);

        $myPropertyList = MyPropertyList::create([
            'name' => $request->name,
            'property_ids' => $request->selectionIds,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Property list updated successfully.');
    }

    public function updateMyPropertyList(Request $request)
    {
        $request->validate([
            'recordId' => 'required|integer|exists:my_property_lists,id',
            'selectionIds' => ['required', 'array'],
            'selectionIds.*' => ['integer'],
        ]);

        $record = MyPropertyList::find($request->recordId);
        if (!$record) {
            return redirect()->back()->withErrors(['recordId' => 'Record does not exist!']);
        }
        $existingIds = $record->property_ids ?? [];
        $newIds = $request->selectionIds;

        $mergedIds = array_values(array_unique(array_merge($existingIds, $newIds)));

        $record->update([
            'property_ids' => $mergedIds,
        ]);

        return redirect()->back()->with('success', 'Property list updated successfully.');
    }


    public function getMyUnlistedList(Request $request)
    {
        $res = MyUnlistedList::where('user_id', $request->user()->id)->get();
        return $res;
    }


    public function createMyUnlistedList(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:my_unlisted_lists,name',
            'zillowData' => ['required'],
        ]);

        $unlistedList = MyUnlistedList::create([
            'name' => $request->name,
            'addresses' => [$request->zillowData], // auto-cast to JSON
            'user_id' => $request->user()->id, // assumes authenticated user
        ]);
        return redirect()->back()->with('success', 'Unlisted list created successfully.');
    }

    public function updateMyUnlistedList(Request $request)
    {
        $request->validate([
            'recordId' => 'required|integer|exists:my_unlisted_lists,id',
            'zillowData' => ['required']
        ]);

        $record = MyUnlistedList::find($request->recordId);
        if (!$record) {
            return redirect()->back()->withErrors(['recordId' => 'Record does not exist!']);
        }

        $existingAddresses = $record->addresses ?? [];
        $newAddresses = [$request->zillowData];

        $merged = array_unique(array_merge($existingAddresses, $newAddresses), SORT_REGULAR);

        $record->update([
            'addresses' => $merged,
        ]);

        return redirect()->back()->with('success', 'Unlisted list updated successfully.');
    }


    public function saveRequestAShowing(Request $request, EmailService $emailService)
    {
        $request->validate([
            'propertyId' => ['required', 'integer'],
            'propertyAddress' => ['required', 'string', 'max:255'],
            'propertyStreet' => ['required', 'string'],
            'propertyCity' => ['required', 'string'],
            'propertyState' => ['required', 'string'],
            'propertyZip' => ['required', 'string'],
            'propertyImage' => ['required', 'string'],
            'preferredDateTime' => ['required', 'date'],
            'userName' => ['required', 'string', 'max:255'],
            'userPhone' => ['required', 'string', 'max:20'],
            'userEmail' => ['required', 'email', 'max:255'],
            'message' => ['nullable', 'string'],
        ]);

        $wholesale = WholesaleProperty::with('user')
            ->where('database_id', $request->input('propertyId'))->first();

        RequestAShowing::create([
            'full_street_address' => $request->input('propertyStreet'),
            'city' => $request->input('propertyCity'),
            'state' => $request->input('propertyState'),
            'zip' => $request->input('propertyZip'),
            'property_id' => $request->input('propertyId'),
            'preferred_time' => $request->input('preferredDateTime'),
            'message' => $request->input('message'),
            'wholesale_id' => $wholesale->id ?? null,
            'user_id' => $request->user()->id,
        ]);

        $config = config('user_email_config.wholesale.wholesale_schedule_showing');
        $propertyAddress = $request->input('propertyAddress');

        if ($wholesale) {
            $emailService->send($wholesale->user, 'wholesale_schedule_showing', $config['template_id'], [
                'subject' => 'Schedule a Showing - ' . $propertyAddress,
                "property_id" => $request->input('propertyId'),
                "property_address" => $propertyAddress,
                "property_image" => $request->input('propertyImage'),
                "preferred_time" => CommonHelper::formatDate($request->input('preferredDateTime')),
                "name" => $request->user()->name,
                "phone" => ValueFormat::formatPhone($request->user()->phone_number),
                "email" => $request->user()->email,
                "message" => $request->input('message'),
                'unsubscribe' => url('unsubscribe/' . $request->user()->uuid)
            ]);
        }

        // Send SAME email to listing agent
        $listingAgentEmail = $this->getListingAgentEmail($request->input('propertyId'));
        if ($listingAgentEmail) {
            $userNameParts = explode(' ', $request->user()->name);
            $userFirstName = $userNameParts[0] ?? '';
            $userLastName = $userNameParts[1] ?? '';

            $emailService->sendCustom(
                $listingAgentEmail,
                '',
                'wholesale_schedule_showing',
                $config['template_id'],
                [
                    'subject' => $request->input('propertyAddress') . ' - An investor buyer lead for you - ' . $userFirstName . ' ' . $userLastName,
                    "property_id" => $request->input('propertyId'),
                    "property_address" => $propertyAddress,
                    "property_image" => $request->input('propertyImage'),
                    "preferred_time" => CommonHelper::formatDate($request->input('preferredDateTime')),
                    "name" => $request->user()->name,
                    "phone" => ValueFormat::formatPhone($request->user()->phone_number),
                    "email" => $request->user()->email,
                    "message" => $request->input('message'),
                    "cc_email" => $request->user()->email,
                    'unsubscribe' => url('unsubscribe/' . $request->user()->uuid)
                ]
            );
        }

        return redirect()->back()->with('success', 'Your showing request has been sent. We’ll get back to you shortly.');
    }

    public function saveInstantOffer(Request $request, EmailService $emailService)
    {

        $request->validate([
            'propertyId' => ['required', 'integer'],
            'propertyAddress' => ['required', 'string', 'max:255'],
            'propertyStreet' => ['required', 'string'],
            'propertyCity' => ['required', 'string'],
            'propertyState' => ['required', 'string'],
            'propertyZip' => ['required', 'string'],
            'propertyTin' => ['nullable', 'string'],
            'propertyOffice' => ['nullable', 'string'],
            'userName' => ['required', 'string', 'max:255'],
            'userContractualName' => ['required', 'string', 'max:255'],
            'userPhone' => ['required', 'string', 'max:20'],
            'userEmail' => ['required', 'email', 'max:255'],
            'offerPrice' => ['required', 'numeric', 'min:100', 'max:99999999'],
            'depositAmount' => ['required', 'numeric', 'min:100', 'max:99999999'],
            'preferredClosingDate' => ['required', 'date'],
            'agentInvolved' => ['required', 'in:Yes,No'],
            'agentName' => ['nullable', 'required_if:agentInvolved,Yes', 'string', 'max:255'],
            'agentEmail' => ['nullable', 'required_if:agentInvolved,Yes', 'email', 'max:255'],
            'agentCommission' => ['nullable', 'required_if:agentInvolved,Yes', 'numeric', 'min:0', 'max:99999999'],
            'note' => ['nullable', 'string'],
        ]);

        $wholesale = WholesaleProperty::where('database_id', $request->input('propertyId'))->first();

        InstantOffer::create([
            'address' => $request->input('propertyAddress'),
            'full_street_address' => $request->input('propertyStreet'),
            'city' => $request->input('propertyCity'),
            'state' => $request->input('propertyState'),
            'zip' => $request->input('propertyZip'),
            'property_id' => $request->input('propertyId'),
            'name' => $request->input('userName'),
            'email' => $request->input('userEmail'),
            'phone' => $request->input('userPhone'),
            'buyer_name_llc' => $request->input('userContractualName'),
            'deposit_price' => $request->input('depositAmount'),
            'offer_price' => $request->input('offerPrice'),
            'preferred_closing_date' => $request->input('preferredClosingDate'),
            'note' => $request->input('note'),
            'agent_name' => $request->input('agentName'),
            'agent_email' => $request->input('agentEmail'),
            'agent_commission' => $request->input('agentCommission'),
            'tin' => $request->input('propertyTin'),
            'assignor_name' => $request->input('propertyOffice'),
            'wholesale_id' => $wholesale->id ?? null,
            'user_id' => $request->user()->id,
        ]);

        if ($wholesale) {
            $config = config('user_email_config.wholesale.wholesale_instant_offer');
            $propertyAddress = $request->input('propertyAddress');
            $emailService->send(
                $wholesale->user,
                'wholesale_schedule_showing',
                $config['template_id'],
                [
                    'subject' => 'Instant Offer - ' . $propertyAddress,
                    "property_id" => $request->input('propertyId'),
                    "property_address" => $propertyAddress,
                    "property_image" => $request->input('propertyImage'),
                    "contractual_name" => $request->input('userContractualName'),
                    "name" => $request->input('userName'),
                    "phone" => ValueFormat::formatPhone($request->input('userPhone')),
                    "email" => $request->input('userEmail'),
                    "offer_price" => ValueFormat::formatPrice($request->input('offerPrice')),
                    "deposit_amount" => ValueFormat::formatPrice($request->input('depositAmount')),
                    "closing" => ValueFormat::formatDateTime($request->input('preferredClosingDate')),
                    "agent_involved" => $request->input('agentName') ? 'Yes' : 'No',
                    "agent_name" => $request->input('agentName'),
                    "agent_email" => $request->input('agentEmail'),
                    "agent_commission" => ValueFormat::formatPrice($request->input('agentCommission')),
                    "note" => $request->input('note'),
                    "unsubscribe" => url('unsubscribe/' . $request->user()->uuid)
                ]
            );
        }

        return redirect()->back()->with('success', 'Your offer has been submitted.');
    }

    public function saveAskAQuestion(Request $request, EmailService $emailService)
    {
        $request->validate([
            'propertyId' => ['required', 'integer'],
            'propertyAddress' => ['required', 'string', 'max:255'],
            'propertyStreet' => ['required', 'string'],
            'propertyCity' => ['required', 'string'],
            'propertyState' => ['required', 'string'],
            'propertyZip' => ['required', 'string'],
            'question' => ['required', 'string', 'max:1000'],
            'userName' => ['required', 'string', 'max:255'],
            'userPhone' => ['required', 'string', 'max:20'],
            'userEmail' => ['required', 'email', 'max:255'],
            'contactBy' => ['required', 'in:Email,Text Message,Call'],
        ]);

        $wholesale = WholesaleProperty::where('database_id', $request->input('propertyId'))->first();
        $propertyId = $request->input('propertyId');
        $name = $request->input('userName');
        $phone = $request->input('userPhone');
        $email = $request->input('userEmail');
        $contactBy = $request->input('contactBy');
        $question = $request->input('question');

        AskQuestion::create([
            'full_street_address' => $request->input('propertyStreet'),
            'city' => $request->input('propertyCity'),
            'state' => $request->input('propertyState'),
            'zip' => $request->input('propertyZip'),
            'name' => $name,
            'email' => $email,
            'phone' => ValueFormat::formatPhone($phone),
            'preferred_contact_method' => $contactBy,
            'property_id' => $propertyId,
            'question' => $question,
            'wholesale_id' => $wholesale->id ?? null,
            'user_id' => $request->user()->id,
        ]);


        $config = config('user_email_config.wholesale.wholesale_ask_question');
        $propertyAddress = $request->input('propertyAddress');

        if ($wholesale) {
            $emailService->send(
                $wholesale->user,
                'wholesale_schedule_showing',
                $config['template_id'],
                [
                    "subject" => "Asked a Question - " . $propertyAddress,
                    "property_id" => $request->input('propertyId'),
                    "property_address" => $propertyAddress,
                    "property_image" => $request->input('propertyImage'),
                    "name" => $name,
                    "phone" => ValueFormat::formatPhone($phone),
                    "email" => $email,
                    "question" => $question,
                    "preffered_contact_via" => $contactBy,
                    "unsubscribe" => url('unsubscribe/' . $request->user()->uuid)
                ]
            );
        }

        // Send SAME email to listing agent
        $listingAgentEmail = $this->getListingAgentEmail($request->input('propertyId'));
        if ($listingAgentEmail) {
            $userNameParts = explode(' ', $name);
            $userFirstName = $userNameParts[0] ?? '';
            $userLastName = $userNameParts[1] ?? '';

            $emailService->sendCustom(
                $listingAgentEmail,
                '',
                'wholesale_ask_question',
                $config['template_id'],
                [
                    'subject' => $request->input('propertyAddress') . ' - An investor buyer lead for you - ' . $userFirstName . ' ' . $userLastName,
                    'property_id' => $request->input('propertyId'),
                    'property_address' => $request->input('propertyAddress'),
                    'property_image' => $request->input('propertyImage'),
                    'name' => $name,
                    'phone' => ValueFormat::formatPhone($phone),
                    'email' => $email,
                    'question' => $question,
                    'preffered_contact_via' => $contactBy,
                    'cc_email' => $email,
                    'unsubscribe' => url('unsubscribe/' . $request->user()->uuid)
                ]
            );
        }

        return redirect()->back()->with('success', 'Thank you for your question. We’ll get back to you shortly.');
    }

    public function shareProperty(Request $request, EmailService $emailService)
    {

        if (!$request->user())
            return redirect()->back()->with('error', 'You should login to share this property');

        $request->validate([
            'propertyId' => ['nullable', 'integer'],
            'listingType' => ['required', 'in:Unlisted,Listed'],
            'propertyUrl' => ['required', 'string'],
            'propertyAddress' => ['required', 'string', 'max:255'],
            'toEmail' => ['required', 'email',],
            'fromEmail' => ['required', 'email',],
            'message' => ['nullable', 'max:2000'],
        ]);


        $config = config('user_email_config.revamp_team.common');
        $propertyAddress = $request->input('propertyAddress');
        $toEmail = $request->input('toEmail');
        $fromEmail = $request->input('fromEmail');
        $propertyId = $request->input('propertyId');
        $listingType = $request->input('listingType');
        $message = $request->input('message');

        $firstName = $request->user()->first_name;
        $userName = $request->user()->name;
        $propertyURL = ($listingType == 'Unlisted') ? url('/search?unlistedid=' . urldecode($propertyAddress)) : url('/search?propertyid=' . trim($propertyId));


        $content = <<<HTML
<div>
  <p>Hey there!</p>
  <p>{$userName} forwarded this property to you.</p>
  <br/>
  <p style="padding: 5px 10px; border: 1px solid #dbdbdb; border-radius: 15px;">$message</p>
  <br/>
  <p><strong>Click this link to see the property: </strong></p>
  <a href="{$propertyURL}">{$propertyAddress}</a>
  <br/>
  <p>Could this be your next deal?</p>
  <p>-The Revamp 365 Team</p>
</div>
HTML;

        $emailService->sendCustom(
            $toEmail,
            '',
            'wholesale_schedule_showing',
            $config['template_id'],
            [
                'subject' => $firstName . ' : Check out this property in ' . $propertyAddress,
                'body' => $content
            ]
        );


        return redirect()->back()->with('success', 'Thank you for your question. We’ll get back to you shortly.');
    }


    public function propertyAskAi(Request $request)
    {
        $key = config('services.chat_gpt.key');
        $messages = $request->input('messages');

        // Check if streaming is requested
        if ($request->input('stream', false)) {
            return $this->streamChatGptResponse($key, $messages);
        }

        // Non-streaming response (fallback)
        $response = Http::withToken($key)->timeout(200)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        return response()->json($response->json());
    }

    private function streamChatGptResponse($key, $messages)
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => 'gpt-4o',
            'messages' => $messages,
            'temperature' => 0.2,
            'stream' => true,
        ];

        $headers = [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json',
        ];

        // Set response headers for streaming
        header('Content-Type: text/plain');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
            echo $data;
            flush();
            return strlen($data);
        });
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($result === false || $httpCode !== 200) {
            // If streaming fails, return error as JSON
            header('Content-Type: application/json');
            return response()->json([
                'error' => 'Streaming failed: ' . ($error ?: 'HTTP ' . $httpCode)
            ], 500);
        }

        exit;
    }

    public function propertyAskAiSettingsMessage(Request $request)
    {
        $listingType = $request->input('listingType');
        return response()->json(PropertyHelper::getAskAiInitialSettingsText($listingType));
    }

    public function requestWholesaleAddress(Request $request, EmailService $emailService)
    {
        $wholesale = WholesaleProperty::with('user')
            ->where('database_id', $request->input('property_id'))->first();

        if (!$wholesale) {
            return response()->json(['error' => 'Wholesale Property not found']);
        }

        $wholesaleUser = $wholesale->user;
        $wholesaleId = $wholesale->id;
        $userName = $request->user()->name;
        $userEmail = $request->user()->email;
        $userPhone = $request->user()->phone_number;
        $userId = $request->user()->id;
        $propertyAddress = $wholesale->full_street_address . ',  ' . $wholesale->city_name . ',  ' . $wholesale->state_or_province . ' ' . $wholesale->zip_code;

        AddressRequest::create([
            'is_agreed' => AddressRequest::STATUS_PENDING,
            'property_id' => $request->input('property_id'),
            'wholesale_id' => $wholesaleId,
            'user_id' => $userId,
        ]);

        $config = config('user_email_config.revamp_team.common');
        $propertyURL = url('/property/search?propertyid=' . $request->input('propertyId'));

        $content = <<<HTML
<div>
  <p><strong>Name:</strong> {$userName}</p>
  <p><strong>Email:</strong> {$userEmail}</p>
  <p><strong>Phone:</strong> {$userPhone}</p>
  <br/>
  <p><strong>Property Detail</strong></p>
  <a href="{$propertyURL}">{$propertyAddress}</a>
</div>
HTML;

        $emailService->send($wholesaleUser, 'address-request', $config['template_id'], [
            'subject' => 'Address Request',
            'body' => $content
        ]);

        return response()->json(['success' => 'Your request has been processing. Thank you for waiting']);
    }

    public function getAiUnlistedDetail(Request $request)
    {
        $address = $request->input('address');
        $data = null;

        $apiHost = config('services.zillow.host');
        $apiKey = config('services.zillow.key');
        $url = "https://zillow-com1.p.rapidapi.com/property";

        $response = Http::withHeaders([
            'x-rapidapi-host' => $apiHost,
            'x-rapidapi-key' => $apiKey,
        ])->get($url, [
                    'address' => $address,
                ]);

        $jsonRes = $response->json();

        return response()->json(['data' => $jsonRes ?? null, 'error' => $jsonRes ? null : 'Could not get the data!']);
    }

    public function addRecentViewProperty(Request $request)
    {

        $userId = $request->user()->id;

        $propertyId = $request->input('id');
        $geoAddress = $request->input('geo_address');
        $streetAddress = $request->input('street_address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $status = $request->input('status');
        $platform = $request->input('platform');
        $useragent = $request->input('useragent');
        $isMobile = $request->input('is_mobile');
        $isLocked = $request->input('is_locked');

        $ip = $request->ip();
        $location = geoip($ip);
        $ipCity = $location->city;
        $ipRegion = $location->state;
        $ipCountry = $location->country;

        $res = PropertyViewHistory::create([
            'property_id' => $propertyId,
            'geo_address' => $geoAddress,
            'street_address' => $streetAddress,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'status' => $status,
            'platform' => $platform,
            'useragent' => $useragent,
            'is_mobile' => $isMobile,
            'ip' => $ip,
            'ip_city' => $ipCity,
            'ip_region' => $ipRegion,
            'ip_country' => $ipCountry,
            'user_id' => $userId,
            'is_locked' => $isLocked
        ]);

        return response()->json(['status' => 'done', 'res' => $res?->id]);
    }

    public function addRecentViewUnlistedProperty(Request $request)
    {
        $userId = $request->user()->id;

        $propertyId = $request->input('zpid');
        $address = $request->input('address');
        $platform = $request->input('platform');
        $useragent = $request->input('useragent');
        $isMobile = $request->input('is_mobile');

        $ip = $request->ip();
        $location = geoip($ip);
        $ipCity = $location->city;
        $ipRegion = $location->state;
        $ipCountry = $location->country;

        $res = UnlistedViewHistory::create([
            'zpid' => $propertyId,
            'address' => $address,
            'platform' => $platform,
            'useragent' => $useragent,
            'is_mobile' => $isMobile,
            'ip' => $ip,
            'ip_city' => $ipCity,
            'ip_region' => $ipRegion,
            'ip_country' => $ipCountry,
            'user_id' => $userId,
        ]);

        return response()->json(['status' => 'done', 'res' => $res?->id]);
    }

    public function addFilterHistory(Request $request)
    {
        $userId = $request->user()->id;

        $ip = $request->ip();
        $location = geoip($ip);
        $ipCity = $location->city;
        $ipRegion = $location->state;
        $ipCountry = $location->country;

        $res = PropertyFilterHistory::create([
            'delta_min' => $request->input('delta_min'),
            'delta_max' => $request->input('delta_max'),
            'est_profit_min' => $request->input('est_profit_min'),
            'est_profit_max' => $request->input('est_profit_max'),
            'est_cashflow_min' => $request->input('est_cashflow_min'),
            'est_cashflow_max' => $request->input('est_cashflow_max'),
            'remarks_public_keywords' => $request->input('remarks_public_keywords'),
            'city_name_keyword' => $request->input('city_name_keyword'),
            'dom_min' => $request->input('dom_min'),
            'dom_max' => $request->input('dom_max'),
            'est_arv_min' => $request->input('est_arv_min'),
            'est_arv_max' => $request->input('est_arv_max'),
            'lot_sqf_min' => $request->input('lot_sqf_min'),
            'lot_sqf_max' => $request->input('lot_sqf_max'),
            'total_finished_sqft_min' => $request->input('total_finished_sqft_min'),
            'total_finished_sqft_max' => $request->input('total_finished_sqft_max'),
            'list_price_min' => $request->input('list_price_min'),
            'list_price_max' => $request->input('list_price_max'),
            'medianrent_min' => $request->input('medianrent_min'),
            'medianrent_max' => $request->input('medianrent_max'),
            'state_or_province_keyword' => $request->input('state_or_province_keyword'),
            'bedrooms_min' => $request->input('bedrooms_min'),
            'bedrooms_max' => $request->input('bedrooms_max'),
            'county' => $request->input('county'),
            'status' => $request->input('status'),
            'list_agent_keyword' => $request->input('list_agent_keyword'),
            'fulladdress_keyword' => $request->input('fulladdress_keyword'),
            'structure_type' => $request->input('structure_type'),
            'mls_number' => $request->input('mls_number'),
            'listing_entry_date_min' => $request->input('listing_entry_date_min'),
            'listing_entry_date_max' => $request->input('listing_entry_date_max'),
            'closed_date_min' => $request->input('closed_date_min'),
            'closed_date_max' => $request->input('closed_date_max'),
            'order_by' => $request->input('order_by'),
            '_limit' => $request->input('_limit'),
            'zip' => $request->input('zip'),
            'comps_sub_prop_id' => $request->input('comps_sub_prop_id'),
            'distance_max' => $request->input('distance_max'),
            'user_lat' => $request->input('user_lat'),
            'user_lng' => $request->input('user_lng'),
            'test' => $request->input('test'),
            'deal_type' => $request->input('deal_type'),
            'year_built_min' => $request->input('year_built_min'),
            'year_built_max' => $request->input('year_built_max'),
            'city_names_avoid' => $request->input('city_names_avoid'),
            'filter_ids' => $request->input('filter_ids'),
            'fulladdress_avoid' => $request->input('fulladdress_avoid'),
            'all_wholesale' => $request->input('all_wholesale'),
            'accuracy_score_value' => $request->input('accuracy_score_value'),
            'accuracy_score_rent' => $request->input('accuracy_score_rent'),
            'school_district_name' => $request->input('school_district_name'),
            'is_mobile' => $request->input('is_mobile'),
            'platform' => $request->input('platform'),
            'useragent' => $request->input('useragent'),

            'ip' => $ip,
            'ip_city' => $ipCity,
            'ip_region' => $ipRegion,
            'ip_country' => $ipCountry,
            'user_id' => $userId,
        ]);

        return response()->json(['status' => 'done', 'res' => $res?->id]);
    }

    public function submitBuyerFinancing(Request $request, EmailService $emailService)
    {
        $request->validate([
            'propertyId' => ['required'],
            'propertyAddress' => ['nullable'],
            'step1' => ['required', 'string'],
            'step2' => ['required', 'string'],
            'step3' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $q1 = $request->input('step1');
        $q2 = $request->input('step2');
        $q3 = $request->input('step3');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $firstname = $request->input('firstName');
        $lastname = $request->input('lastName');

        BuyerFinancing::create([
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'email' => $email,
            'property_id' => $request->input('propertyId'),
            'first_name' => $firstname,
            'last_name' => $lastname,
            'phone' => $phone,
            'user_id' => $request->user()->id,
        ]);

        $config = config('user_email_config.revamp_team.common');
        $propertyURL = url('/property/search?propertyid=' . $request->input('propertyId'));
        $propertyAddress = $request->input('propertyAddress');

        $content = <<<HTML
<div>
  <p><strong>First Name:</strong> {$firstname}</p>
  <p><strong>Last Name:</strong> {$lastname}</p>
  <p><strong>Email:</strong> {$email}</p>
  <p><strong>Phone:</strong> {$phone}</p>
  <br/>
  <p><strong>Where are you in your investment purchase journey?</strong></p>
  <p>{$q1}</p>
  <p><strong>When are you looking to close?</strong></p>
  <p>{$q2}</p>
  <p><strong>What type of loan are you interested in?</strong></p>
  <p>{$q3}</p>
  <br/>
  <p><strong>Property Detail</strong></p>
  <a href="{$propertyURL}">{$propertyAddress}</a>
</div>
HTML;

        $emailService->sendSystem($config['template_id'], [
            'subject' => 'New Funding Request',
            'body' => $content
        ]);

        return redirect()->back()->with('success', 'Thank you for submitting your request, We’ll get back to you shortly.');
    }

    public function marketDataPage(Request $request)
    {
        // Get all states
        $states = CountyState::select('state')
            ->groupBy('state')
            ->orderBy('state')
            ->get()
            ->map(function ($state) {
                return [
                    'key' => $state->state,
                    'value' => $state->state
                ];
            });

        // Get all counties
        $counties = CountyState::select('county', 'display', 'state')
            ->orderBy('display')
            ->get()
            ->map(function ($county) {
                return [
                    'key' => $county->county,
                    'value' => $county->display
                ];
            });

        // For now, we'll use empty arrays for cities and zips
        // These can be populated from a database table later
        $cities = [];
        $zips = [];

        return Inertia::render('market-data/MarketData', [
            'counties' => $counties,
            'states' => $states,
            'cities' => $cities,
            'zips' => $zips
        ]);
    }



    private function getListingAgentEmail($propertyId)
    {
        // TEMPORARY: For testing, return your email address
        // return 'example@gmail.com'; // Replace with your actual email
        // Get property data from your database or API
        $dbApiHelper = new \App\Helper\DBApiHelper();
        $request = new Request(['id' => $propertyId]);
        $response = $dbApiHelper->getPropertyData($request);
        $propertyData = json_decode($response->getContent(), true);

        return $propertyData['data']['list_agent_email'] ?? null;
    }
}
