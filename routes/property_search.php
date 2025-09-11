<?php

use App\Http\Controllers\PropertySearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UpdateLastLogin;

Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::redirect('property', url('/property/search'));
    Route::get('property/search', [PropertySearchController::class, 'index'])
        ->name('property.search');
    Route::post('property/search/save-search', [PropertySearchController::class, 'saveSearch'])
        ->name('property.search.save_search');
    Route::get('property/search/save-search/get-list', [PropertySearchController::class, 'getSavedSearchList'])
        ->name('property.search.save_search.get_list');

    Route::get('property/search/save-search/get-by-id/{id}', [PropertySearchController::class, 'getSavedSearchById'])
        ->name('property.search.save_search.get_by_id');

    Route::post('property/search/save-search/set_default', [PropertySearchController::class, 'setSaveSearchDefault'])
        ->name('property.search.save_search.set_default');

    Route::post('property/search/save-search/delete', [PropertySearchController::class, 'deleteSavedSearchById'])
        ->name('property.search.save_search.delete');

    Route::post('property/search/hide-property', [PropertySearchController::class, 'propertyHide'])
        ->name('property.search.hide_property');

    Route::get('property/search/get-hidden-properties', [PropertySearchController::class, 'getHiddenProperties'])
        ->name('property.search.get-hidden-properties');

    // My Property List
    Route::get('property/my-list/get-property', [PropertySearchController::class, 'getMyPropertyList'])
        ->name('property.my_list.get_property');

    Route::post('property/my-list/create-property', [PropertySearchController::class, 'createMyPropertyList'])
        ->name('property.my_list.create_property');

    Route::post('property/my-list/update-property', [PropertySearchController::class, 'updateMyPropertyList'])
        ->name('property.my_list.update_property');

    // My Unlisted List
    Route::get('property/my-list/get-unlisted', [PropertySearchController::class, 'getMyUnlistedList'])
        ->name('property.my_list.get_unlisted');

    Route::post('property/my-list/create-unlisted', [PropertySearchController::class, 'createMyUnlistedList'])
        ->name('property.my_list.create_unlisted');

    Route::post('property/my-list/update-unlisted', [PropertySearchController::class, 'updateMyUnlistedList'])
        ->name('property.my_list.update_unlisted');

    Route::post('property/request/showing', [PropertySearchController::class, 'saveRequestAShowing'])
        ->name('property.request.showing');

    Route::post('property/request/instant_offer', [PropertySearchController::class, 'saveInstantOffer'])
        ->name('property.request.instant_offer');

    Route::post('property/request/ask_question', [PropertySearchController::class, 'saveAskAQuestion'])
        ->name('property.request.ask_question');

    Route::post('property/request/share-property', [PropertySearchController::class, 'shareProperty'])
        ->name('property.request.share_property');

    Route::post('property/request/ask-ai', [PropertySearchController::class, 'propertyAskAi'])
        ->name('property.request.ask_ai');

    Route::get('property/request/ask-ai-setting-message', [PropertySearchController::class, 'propertyAskAiSettingsMessage'])
        ->name('property.request.ask_ai-setting-message');

    Route::post('property/request/wholesale-address', [PropertySearchController::class, 'requestWholesaleAddress'])
        ->name('property.request.wholesale_address');


    Route::get('property/request/ask-ai-unlisted-details', [PropertySearchController::class, 'getAiUnlistedDetail'])
        ->name('property.request.ask_ai_unlisted_details');


    Route::get('property/request/ask-ai-unlisted-details', [PropertySearchController::class, 'getAiUnlistedDetail'])
        ->name('property.request.ask_ai_unlisted_details');

    Route::post('property/request/buyer-financing', [PropertySearchController::class, 'submitBuyerFinancing'])
        ->name('property.request.buyer_financing');

    Route::get('my-reports', [PropertySearchController::class, 'getMyReports'])
        ->name('my-reports');

    Route::get('market-data', [PropertySearchController::class, 'marketDataPage'])
        ->name('market-data'); 
});

Route::post('property/recent-view', [PropertySearchController::class, 'addRecentViewProperty'])
    ->name('property.recent_view');

Route::post('property/recent-view-unlisted', [PropertySearchController::class, 'addRecentViewUnlistedProperty'])
    ->name('property.recent_view_unlisted');

Route::post('filter/update-history', [PropertySearchController::class, 'addFilterHistory'])
    ->name('filter.update_history');

