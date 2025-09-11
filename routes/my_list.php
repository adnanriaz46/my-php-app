<?php

use App\Http\Middleware\UpdateLastLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyListController;

Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::get('my-list/properties', [MyListController::class, 'propertiesIndex'])
        ->name('my_list.properties');

    Route::post('my-list/properties/delete', [MyListController::class, 'propertiesDelete'])
        ->name('my_list.properties.delete');

    Route::post('my-list/properties/update', [MyListController::class, 'propertiesUpdate'])
        ->name('my_list.properties.update');

    Route::post('my-list/properties/update-property-ids', [MyListController::class, 'propertiesUpdatePropertyIds'])
        ->name('my_list.properties.update_property_ids');


    Route::get('my-list/unlisted', [MyListController::class, 'unlistedIndex'])
        ->name('my_list.unlisted');

    Route::post('my-list/unlisted/delete', [MyListController::class, 'unlistedDelete'])
        ->name('my_list.unlisted.delete');

    Route::post('my-list/unlisted/update', [MyListController::class, 'unlistedUpdate'])
        ->name('my_list.unlisted.update');

    Route::post('my-list/unlisted/update-addresses', [MyListController::class, 'unlistedUpdateAddresses'])
        ->name('my_list.unlisted.update_addresses');

    Route::get('my-list/buyers', [MyListController::class, 'buyersIndex'])
        ->name('my_list.buyers');

    Route::get('my-list/suppressed', [MyListController::class, 'suppressedIndex'])
        ->name('my_list.suppressed');

    Route::post('my-list/suppressed/remove', [MyListController::class, 'suppressedRemove'])
        ->name('my_list.suppressed.remove');
});

Route::prefix('api')->group(function () {
    Route::get('/buyer-lists', [MyListController::class, 'getBuyerLists']);
Route::post('/buyer-lists', [MyListController::class, 'saveBuyerList']);
Route::delete('/buyer-lists/{name}', [MyListController::class, 'deleteBuyerList']);
});