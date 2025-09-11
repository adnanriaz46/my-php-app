<?php

namespace App\Http\Controllers;

use App\Helper\CommonHelper;
use App\Models\MyPropertyList;
use App\Models\MyUnlistedList;
use App\Models\SuppressProperty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MyBuyerList;
use App\Models\MyBuyerListBuyer;
class MyListController extends Controller
{

    public function propertiesIndex(Request $request)
    {
        $user = $request->user();
        $data = MyPropertyList::where('user_id', $user->id)->get();

        return Inertia::render('my-list/MyListProperties', [
            'list' => $data,
            'user' => $user,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function propertiesDelete(Request $request)
    {
        $id = $request->input('id');
        $d = MyPropertyList::where('id', $id)->delete();
        return response()->json(['success' => 'Your list has been deleted']);
    }

    public function propertiesUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id' => ['required', 'numeric', 'exists:my_property_lists,id'],
            'name' => ['required', 'unique:my_property_lists,name,' . $request->input('id')],
        ]);

        $id = $request->input('id');
        $name = $request->input('name');
        MyPropertyList::where('id', $id)->update(['name' => $name]);
        return back()->with('success', 'My List has been updated!');
    }

    public function propertiesUpdatePropertyIds(Request $request)
    {
        $id = $request->input('id');
        $propertyIdsToRemove = $request->input('property_ids');

        $list = MyPropertyList::findOrFail($id);

        $existingPropertyIds = is_array($list->property_ids)
            ? $list->property_ids
            : json_decode($list->property_ids, true);

        $newPropertyIds = array_values(array_diff($existingPropertyIds, $propertyIdsToRemove));

        $list->update(['property_ids' => $newPropertyIds]);

        return response()->json(['success' => 'Your list has been updated']);
    }

    public function unlistedIndex(Request $request)
    {
        $user = $request->user();
        $data = MyUnlistedList::where('user_id', $user->id)->get();

        return Inertia::render('my-list/MyListUnlisted', [
            'list' => $data,
            'user' => $user,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function unlistedDelete(Request $request)
    {
        $id = $request->input('id');
        $d = MyUnlistedList::where('id', $id)->delete();
        return response()->json(['success' => 'Your list has been deleted']);
    }

    public function unlistedUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id' => ['required', 'numeric', 'exists:my_unlisted_lists,id'],
            'name' => ['required', 'unique:my_unlisted_lists,name,' . $request->input('id')],
        ]);

        $id = $request->input('id');
        $name = $request->input('name');
        MyUnlistedList::where('id', $id)->update(['name' => $name]);
        return back()->with('success', 'My List has been updated!');
    }

    public function unlistedUpdateAddresses(Request $request)
    {
        $id = $request->input('id');
        $addressesToRemove = $request->input('addresses');

        $list = MyUnlistedList::findOrFail($id);

        $existingAddresses = is_array($list->addresses)
            ? $list->addresses
            : json_decode($list->addresses, true);

        // Create new array by filtering out matches
        $newAddresses = array_values(array_filter($existingAddresses, function ($existing) use ($addressesToRemove) {
            foreach ($addressesToRemove as $remove) {
                if (
                    $existing['zpid'] == $remove['zpid'] &&
                    $existing['list_price'] == $remove['list_price'] &&
                    $existing['zEstimateRent'] == $remove['zEstimateRent']
                ) {
                    return false; // skip this one (remove it)
                }
            }
            return true; // keep this one
        }));

        $list->update(['addresses' => $newAddresses]);
        return response()->json(['success' => 'Your list has been updated']);
    }


    public function buyersIndex(Request $request)
    {
        $user = $request->user();
        //$data = MyPropertyList::where('user_id', $user->id)->get();
        $data = [];
        return Inertia::render('my-list/MyListBuyers', [
            'data' => $data,
            'user' => $user,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }


    public function suppressedIndex(Request $request)
    {
        $userId = $request->user()->id;
        $data = SuppressProperty::where('user_id', $userId)->pluck('property_id')
            ->toArray();

        return Inertia::render('my-list/MyListSuppressed', [
            'idList' => $data,
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }


    public function suppressedRemove(Request $request)
    {
        $userId = $request->user()->id;
        $propertyIds = $request->input('property_ids');

        if (!is_array($propertyIds) || empty($propertyIds)) {
            return response()->json(['error' => 'Invalid property_ids. Must be a non-empty array.']);
        }

        SuppressProperty::where('user_id', $userId)
            ->whereIn('property_id', $propertyIds)
            ->delete();

        return response()->json(['success' => 'Property has been removed from suppressed']);
    }

    // Fetch all buyer lists for the current user
public function getBuyerLists(Request $request)
{
    $lists = MyBuyerList::where('user_id', $request->user()->id)
        ->with('buyers')
        ->get()
        ->map(function ($list) {
            return [
                'name' => $list->name,
                'buyers' => $list->buyers->map(function ($b) {
                    return [
                        'investor_identifier' => $b->investor_identifier,
                        // Property address fields
                        'mrp_fullstreet' => $b->mrp_fullstreet,
                        'mrp_city' => $b->mrp_city,
                        'mrp_state' => $b->mrp_state,
                        'mrp_zip' => $b->mrp_zip,
                        'mrp_sales_price' => $b->mrp_sales_price,
                        'mrp_purchase' => $b->mrp_purchase,
                        'mrp_beds' => $b->mrp_beds,
                        'mrp_bath' => $b->mrp_bath,
                        'mrp_sqft' => $b->mrp_sqft,
                        // ADD MAILING ADDRESS FIELDS
                        'MailingFullStreetAddress' => $b->MailingFullStreetAddress,
                        'MailingCity' => $b->MailingCity,
                        'MailingState' => $b->MailingState,
                        'MailingZIP5' => $b->MailingZIP5,
                    ];
                }),
            ];
        });
    return response()->json($lists);
}

// Save a buyer list
public function saveBuyerList(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'buyers' => 'required|array',
    ]);
    $list = MyBuyerList::updateOrCreate(
        ['user_id' => $request->user()->id, 'name' => $request->name],
        []
    );
    
    // Get existing buyer identifiers in this specific list to avoid duplicates within the list
    $existingBuyerIds = MyBuyerListBuyer::where('my_buyer_list_id', $list->id)
        ->pluck('investor_identifier')
        ->toArray();
    
    // Add only new buyers to this list (allow same buyer in different lists)
    foreach ($request->buyers as $buyer) {
        if (!in_array($buyer['investor_identifier'], $existingBuyerIds)) {
            MyBuyerListBuyer::create([
                'my_buyer_list_id' => $list->id,
                'investor_identifier' => $buyer['investor_identifier'],
                // Property address fields
                'mrp_fullstreet' => $buyer['mrp_fullstreet'] ?? null,
                'mrp_city' => $buyer['mrp_city'] ?? null,
                'mrp_state' => $buyer['mrp_state'] ?? null,
                'mrp_zip' => $buyer['mrp_zip'] ?? null,
                'mrp_sales_price' => $buyer['mrp_sales_price'] ?? null,
                'mrp_purchase' => $buyer['mrp_purchase'] ?? null,
                'mrp_beds' => $buyer['mrp_beds'] ?? null,
                'mrp_bath' => $buyer['mrp_bath'] ?? null,
                'mrp_sqft' => $buyer['mrp_sqft'] ?? null,
                // ADD MAILING ADDRESS FIELDS - THIS IS WHAT WAS MISSING!
                'MailingFullStreetAddress' => $buyer['MailingFullStreetAddress'] ?? null,
                'MailingCity' => $buyer['MailingCity'] ?? null,
                'MailingState' => $buyer['MailingState'] ?? null,
                'MailingZIP5' => $buyer['MailingZIP5'] ?? null,
            ]);
        }
    }
    return response()->json(['success' => true]);
}

// Delete a buyer list
public function deleteBuyerList(Request $request, $name)
{
    $user = $request->user();
    $list = MyBuyerList::where('user_id', $user->id)
        ->where('name', $name)
        ->first();
    
    if (!$list) {
        return response()->json(['error' => 'List not found'], 404);
    }
    
    // Delete the list and all associated buyers
    MyBuyerListBuyer::where('my_buyer_list_id', $list->id)->delete();
    $list->delete();
    
    return response()->json(['success' => 'List deleted successfully']);
}
}
