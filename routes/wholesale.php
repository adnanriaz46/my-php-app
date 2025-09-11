<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WholesaleController;
use App\Http\Middleware\UpdateLastLogin;

Route::middleware(['auth', 'user.types:premium,admin', UpdateLastLogin::class])->group(function () {
    Route::get('my-properties', [WholesaleController::class, 'index'])
        ->name('my_properties');

    Route::post('my-properties/user-agreement', [WholesaleController::class, 'userAgreement'])
        ->name('my_properties.user_agreement');

    Route::get('my-properties/upload-property', [WholesaleController::class, 'uploadPropertyPage'])
        ->name('my_properties.upload_property');
    Route::post('my-properties/upload_property_submit', [WholesaleController::class, 'uploadPropertySubmit'])
        ->name('my_properties.upload_property_submit');

    Route::get('my-properties/edit-property/{id}', [WholesaleController::class, 'editPropertyPage'])
        ->name('my_properties.edit_property');

    Route::post('my-properties/upload-image', [WholesaleController::class, 'uploadWholesaleImage'])
        ->name('my-property.upload_image');

    Route::post('my-properties/edit-property-submit', [WholesaleController::class, 'editPropertySubmit'])
        ->name('my_properties.edit_property_submit');

    Route::post('my-properties/action/cancel', [WholesaleController::class, 'cancelProperty'])
        ->name('my_properties.action.cancel');

    Route::get('my-properties/view-list/{id}', [WholesaleController::class, 'viewListPage'])
        ->name('my_properties.view_list');

    Route::get('my-properties/address-requests/{id}', [WholesaleController::class, 'addressRequest'])
        ->name('my_properties.address_requests');

    Route::post('my-properties/address-requests-process', [WholesaleController::class, 'processWholesaleAddressRequest'])
        ->name('my_properties.address_requests_process');

    Route::post('my-properties/is-address-allowed', [WholesaleController::class, 'isAddressAllowed'])
        ->name('my_properties.is_address_allowed');
});



