<?php

use App\Models\Testimonial;
use Illuminate\Support\Facades\Route;
use App\Helper\LocationHelper;
use App\Helper\AccountHelper;

Route::get('data/get-counties', function () {
    return LocationHelper::getComboboxCounties();
})->name('get.data.combobox_counties');

Route::get('data/get-school-districts', [LocationHelper::class, 'getComboboxSchoolDistricts'])
    ->name('get.data.combobox_school_districts');

Route::get('data/get-upgrade-feature', function () {
    return AccountHelper::getUpgradeFeatures();
})->name('get.data.upgrade_features');


Route::get('data/buyer-financing-form-data', function () {
    return AccountHelper::getBuyerFinancingFormData();
})->name('get.data.buyer_financing_form_data');


Route::get('data/get-location-data', function () {
    return LocationHelper::getTestLocationData();
})->name('get.data.test_location_data');

Route::get('data/get-place-location-data', [LocationHelper::class, 'getLocationPlaceData'])
    ->name('get.data.location_place_data');

Route::get('data/get-zillow-place-data', [LocationHelper::class, 'getZillowLocationData'])
    ->name('get.data.zillow_place_data');

Route::get('data/get-zillow-property-data', [LocationHelper::class, 'getZillowPropertyData'])
    ->name('get.data.zillow_property_data');


Route::get('data/get-ownership-data-old', [LocationHelper::class, 'getOwnershipDataOLD'])
    ->name('get.data.ownership_data_old');

    
Route::get('data/get-testimonials', [Testimonial::class, 'get'])
    ->name('get.data.testimonials');

