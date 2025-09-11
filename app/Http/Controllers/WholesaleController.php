<?php

namespace App\Http\Controllers;

use App\Helper\CommonHelper;
use App\Models\AddressRequest;
use App\Models\AwsS3History;
use App\Models\PropertyViewHistory;
use App\Models\User;
use App\Models\WholesaleProperty;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Http\Response;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        $agreed = Auth()->user()->wholesale_enabled;

        $data = WholesaleProperty::where('user_id', $request->user()->id)
            ->withCount(['viewHistories', 'viewAddressRequests as pending_address_requests_count' => function ($query) {
                $query->where('is_agreed', 0);
            },])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('my-properties/MyProperties', [
            'list' => $data,
            'agreed' => $agreed,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function userAgreement(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId)->update(['wholesale_enabled' => true]);

        $res['success'] = $user ? 'You’ve agreed to the Property Upload Terms.' : null;
        $res['error'] = !$user ? 'Something went wrong! Could not process your request!' : null;

        return response()->json($res);
    }

    public function uploadPropertyPage(Request $request)
    {
        $agreed = Auth()->user()->wholesale_enabled;

        return Inertia::render('my-properties/UploadWholesaleProperty', [
            'agreed' => $agreed,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function editPropertyPage(Request $request, $id)
    {
        $agreed = Auth()->user()->wholesale_enabled;

        $data = WholesaleProperty::findOrFail($id);


        return Inertia::render('my-properties/EditWholesaleProperty', [
            'id' => $id,
            'data' => $data,
            'agreed' => $agreed,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function uploadPropertySubmit(Request $request)
    {
        $request->validate([
            'manualAddress' => 'required|boolean',
            'geoAddress' => 'nullable|string',
            'fullStreetAddress' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'county' => 'required|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'tin' => 'nullable|string',
            'zoning' => 'nullable|string',
            'structureType' => 'required|string',
            'status' => 'required|string',
            'finishedSqft' => 'required|numeric',
            'lotSize' => 'required|numeric',
            'bedrooms' => 'required|numeric|between:0,20',
            'bathrooms' => 'required|numeric|between:0,20',
            'yearBuilt' => 'required|numeric|between:1900,2100',
            'hoa' => 'nullable|boolean',
            'heatType' => 'nullable|array',
            'heatType.*' => 'string',
            'coolType' => 'nullable|array',
            'coolType.*' => 'string',
            'annualTax' => 'nullable|numeric',
            'taxAssessment' => 'nullable|numeric',
            'listPrice' => 'required|numeric',
            'avm' => 'nullable|numeric',
            'arv' => 'nullable|numeric',
            'avgRent' => 'nullable|numeric',
            'estRentRehab' => 'nullable|numeric',
            'estFlipRehab' => 'nullable|numeric',
            'estCashflow' => 'nullable|numeric',
            'estFlipProfit' => 'nullable|numeric',
            'municipality' => 'nullable|string',
            'schoolDistrict' => 'nullable|string',
            'listingOffice' => 'required|string',
            'listingAgent' => 'required|string',
            'listingAgentEmail' => 'required|email',
            'remark' => 'required|string',
        ]);


        $coolType = '';
        $heatType = '';
        if (is_array($request->input('coolType'))) {
            $coolType = implode(',', $request->input('coolType'));
        }
        if (is_array($request->input('heatType'))) {
            $heatType = implode(',', $request->input('heatType') ?? []);
        }

        $property = WholesaleProperty::create([
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'baths' => $request->input('bedrooms'),
            'beds' => $request->input('bathrooms'),
            'structure_type' => $request->input('structureType'),
            'total_finished_sqft' => $request->input('finishedSqft'),
            'lot_sqft' => $request->input('lotSize'),
            'year_built' => $request->input('yearBuilt'),
            'cool_type' => $coolType,
            'heat_type' => $heatType,
            'tax_annual_amount' => $request->input('annualTax'),
            'tax_assessed_value' => $request->input('taxAssessment'),
            'tax_id_number' => $request->input('tin'),
            'zoning' => $request->input('zoning'),
            'full_street_address' => $request->input('fullStreetAddress'),
            'city_name' => $request->input('city'),
            'state_or_province' => $request->input('state'),
            'zip_code' => $request->input('zip'),
            'geo_address' => $request->input('geoAddress'),
            'county' => $request->input('county'),
            'status' => $request->input('status'),
            'list_price' => $request->input('listPrice'),
            'hoa' => $request->input('hoa'),
            'listing_agent' => $request->input('listingAgent'),
            'listing_agent_email' => $request->input('listingAgentEmail'),
            'listing_office' => $request->input('listingOffice'),
            'school_district' => $request->input('schoolDistrict'),
            'municipality' => $request->input('municipality'),
            'remarks_public' => $request->input('remark'),
            'seller_arv' => $request->input('arv'),
            'seller_avg_rent' => $request->input('avgRent'),
            'seller_avm' => $request->input('avm'),
            'seller_est_cashflow' => $request->input('estCashflow'),
            'seller_est_flip_profit' => $request->input('estFlipProfit'),
            'seller_est_flip_rehab' => $request->input('estFlipRehab'),
            'seller_est_rental_rehab' => $request->input('estRentRehab'),
            'manual_address' => $request->input('manualAddress'),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with(['success' => 'Your property has been uploaded!', 'recordId' => $property?->id ?? null]);
    }

    public function editPropertySubmit(Request $request)
    {

        $request->validate([
            'wholesaleId' => 'required|integer|exists:wholesale_properties,id',

            'manualAddress' => 'required|boolean',
            'fullStreetAddress' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'county' => 'required|string|max:100',

            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',

            'status' => 'required|string|max:100',
            'tin' => 'nullable|string|max:100',
            'zoning' => 'nullable|string|max:100',
            'structureType' => 'required|string|max:100',

            'finishedSqft' => 'required|numeric|min:0',
            'lotSize' => 'required|numeric|min:0',
            'bedrooms' => 'required|numeric|min:0',
            'bathrooms' => 'required|numeric|min:0',
            'yearBuilt' => 'required|numeric|digits:4',

            'hoa' => 'nullable|boolean',

            'heatType' => 'nullable|array',
            'heatType.*' => 'string',
            'coolType' => 'nullable|array',
            'coolType.*' => 'string',

            'annualTax' => 'nullable|numeric|min:0',
            'taxAssessment' => 'nullable|numeric|min:0',

            'listPrice' => 'required|numeric|min:0',
            'avm' => 'nullable|numeric|min:0',
            'arv' => 'nullable|numeric|min:0',
            'avgRent' => 'nullable|numeric|min:0',
            'estRentRehab' => 'nullable|numeric|min:0',
            'estFlipRehab' => 'nullable|numeric|min:0',
            'estCashflow' => 'nullable|numeric|min:0',
            'estFlipProfit' => 'nullable|numeric|min:0',

            'municipality' => 'nullable|string|max:100',
            'schoolDistrict' => 'nullable|string|max:100',

            'listingOffice' => 'required|string|max:100',
            'listingAgent' => 'required|string|max:100',
            'listingAgentEmail' => 'required|email|max:255',

            'remark' => 'required|string|max:5000',

            'images' => 'nullable|array',
            'images.*' => 'string',

            'showAddress' => 'nullable|boolean',

            'closedPrice' => 'required_if:status,Closed|nullable|numeric|min:0',
            'closedDate' => 'required_if:status,Closed|nullable|date',
        ]);

        $wholesaleId = request()->input('wholesaleId');

        $coolType = is_array($request->input('coolType')) ? implode(',', $request->input('coolType')) : null;
        $heatType = is_array($request->input('heatType')) ? implode(',', $request->input('heatType')) : null;
        $images = $request->input('images');

        $property = WholesaleProperty::find($wholesaleId);

        $property->update([
            'geo_address' => $request->input('geoAddress'),
            'manual_address' => $request->boolean('manualAddress'),
            'full_street_address' => $request->input('fullStreetAddress'),
            'city_name' => $request->input('city'),
            'state_or_province' => $request->input('state'),
            'zip_code' => $request->input('zip'),
            'county' => $request->input('county'),

            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),

            'status' => $request->input('status'),
            'tax_id_number' => $request->input('tin'),
            'zoning' => $request->input('zoning'),
            'structure_type' => $request->input('structureType'),

            'total_finished_sqft' => $request->input('finishedSqft'),
            'lot_sqft' => $request->input('lotSize'),
            'beds' => $request->input('bedrooms'),
            'baths' => $request->input('bathrooms'),
            'year_built' => $request->input('yearBuilt'),

            'hoa' => $request->boolean('hoa'),
            'heat_type' => $heatType,
            'cool_type' => $coolType,

            'tax_annual_amount' => $request->input('annualTax'),
            'tax_assessed_value' => $request->input('taxAssessment'),

            'list_price' => $request->input('listPrice'),
            'seller_avm' => $request->input('avm'),
            'seller_arv' => $request->input('arv'),
            'seller_avg_rent' => $request->input('avgRent'),
            'seller_est_rental_rehab' => $request->input('estRentRehab'),
            'seller_est_flip_rehab' => $request->input('estFlipRehab'),
            'seller_est_cashflow' => $request->input('estCashflow'),
            'seller_est_flip_profit' => $request->input('estFlipProfit'),

            'municipality' => $request->input('municipality'),
            'school_district' => $request->input('schoolDistrict'),

            'listing_office' => $request->input('listingOffice'),
            'listing_agent' => $request->input('listingAgent'),
            'listing_agent_email' => $request->input('listingAgentEmail'),
            'remarks_public' => $request->input('remark'),

            'images' => $images,

            'show_full_address' => $request->boolean('showAddress') ?? false,

            'closed_price' => $request->input('closedPrice'),
            'closed_date' => $request->input('closedDate'),

            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with([
            'success' => 'Your property has been updated!',
            'recordId' => $property->id,
        ]);
    }


    public function cancelProperty(Request $request)
    {
        $id = $request->input('id');
        $rec = WholesaleProperty::find($id);
        if ($rec) {
            $rec->update(['status' => 'Canceled']);
            return response()->json(['success' => "This property cancellation request has been submitted."]);
        }
        return response()->json(['error' => "Could not find the record, Please refresh the pages"]);
    }

    public function uploadWholesaleImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,bmp,gif,webp,heic,heif,jpg|max:15000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid request',
                'errors' => $validator->errors()->get('image')
            ]);
        }

        $userId = $request->user()->id;

        if ($request->hasFile('image')) {
            try {
                $path = "wholesale/" . $userId . '/';
                $filename = uniqid() . '.webp';
                $fullPath = $path . $filename;

                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->file('image')->getPathname());
                $resizedImage = $image->scaleDown(1024, 1024)->toWebp(90);

                Storage::disk('s3')->put($fullPath, (string)$resizedImage, 'public');

                $url = Storage::disk('s3')->url($fullPath);
                AwsS3History::addHistory('wholesale', $fullPath, $url, $request->user()->id);
                if ($url) {
                    return response()->json(['success' => 'Image has been uploaded!', 'image' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('wholesale', null, null, $request->user()->id, $e->getMessage());
            }

            return response()->json(['error' => 'Could not be upload your file.', 'image' => null]);
        }
        return response()->json(['error' => 'File could not be uploaded', 'image' => null]);
    }

    public function addressRequest(Request $request, $id)
    {
        $agreed = Auth()->user()->wholesale_enabled;

        $ws = WholesaleProperty::findOrFail($id);
        $data = AddressRequest::with('user')->where('property_id', $ws->database_id)
            ->orderBy('id', 'desc')->limit(100)->get();
        $count = AddressRequest::where('property_id', $ws->database_id)
            ->orderBy('id', 'desc')->count();
        return Inertia::render('my-properties/AddressRequestWholesaleProperty', [
            'id' => $id,
            'data' => $data,
            'count' => $count,
            'agreed' => $agreed,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function processWholesaleAddressRequest(Request $request, EmailService $emailService)
    {
        $id = $request->input('id');
        $addressRec = AddressRequest::with('user')->find($id);
        $wholesaleId = $request->input('wholesale_id');
        $type = $request->input('type');
        $approval = AddressRequest::STATUS_PENDING;
        if ($type == 'approved') {
            $approval = AddressRequest::STATUS_APPROVED;
        } else if ($type == 'rejected') {
            $approval = AddressRequest::STATUS_REJECTED;
        }

        AddressRequest::where('property_id', $addressRec->property_id)->where('user_id', $addressRec->user_id)->update([
            'is_agreed' => $approval,
            'wholesale_id' => $wholesaleId
        ]);

        $config = config('user_email_config.wholesale.address_request_approval');
        $approvalString = $approval == 1 ? 'Approved' : 'Rejected';
        $propertyURL = url('/property/search?propertyid=' . $addressRec->property_id);

        $content = <<<HTML
<div>
  <br/>
  <p><strong>You address request for request property has been {$approvalString}</strong></p>
  <br/>
  <p><strong>Property Detail</strong></p>
  <a href="{$propertyURL}">Property Link</a>
</div>
HTML;
        $emailService->send($addressRec->user, 'address_request_approval', $config['template_id'], [
            'subject' => 'Address Request has been processed',
            'body' => $content,
            'unsubscribe' => url('unsubscribe/' . $addressRec->user->uuid)
        ]);

        return response()->json(['success' => 'Request has been processed! Will be notified to the user']);
    }


    public function viewListPage(Request $request, $id)
    {
        $agreed = Auth()->user()->wholesale_enabled;

        $ws = WholesaleProperty::findOrFail($id);
        $data = PropertyViewHistory::with('user')->where('property_id', $ws->database_id)
            ->orderBy('id', 'desc')->limit(100)->get();
        $count = PropertyViewHistory::where('property_id', $ws->database_id)
            ->orderBy('id', 'desc')->count();


        return Inertia::render('my-properties/ViewListWholesaleProperty', [
            'id' => $id,
            'data' => $data,
            'count' => $count,
            'agreed' => $agreed,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function isAddressAllowed(Request $request)
    {
        $userId = $request->user()->id;
        $propertyId = $request->input('property_id');

        $wholesale = WholesaleProperty::select('id', 'show_full_address')->where('database_id', $propertyId)->first();
        if ($wholesale) {
            if ($wholesale->show_full_address) {
                return response()->json(['success' => 'Yes', 'data' => true]);
            } else {
                $addressRequest = AddressRequest::select('is_agreed')
                    ->where('property_id', $propertyId)->where('user_id', $userId)
                    ->first();
                if ($addressRequest->is_agreed == 1) {
                    return response()->json(['success' => 'Yes', 'data' => true]);
                }
            }
        }

        return response()->json(['success' => 'Yes', 'data' => false]);
    }

}
