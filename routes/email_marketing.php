<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailMarketingController;

Route::middleware(['auth', 'user.types:premium,admin', \App\Http\Middleware\UpdateLastLogin::class])->group(function () {

    $_route = 'email-marketing';
    Route::get($_route, [EmailMarketingController::class, 'index'])
        ->name($_route);
    Route::get($_route . '/contacts', [EmailMarketingController::class, 'contacts'])
        ->name($_route . '.contacts');

    // Route::get($_route . '/campaign', [EmailMarketingController::class, 'campaign'])
    //     ->name($_route . '.campaign');

    Route::post($_route . '/contacts/upload', [EmailMarketingController::class, 'uploadContacts'])
        ->name($_route . '.contacts.upload');

    Route::get($_route . '/contacts/get_contacts', [EmailMarketingController::class, 'getContacts'])
        ->name($_route . '.contacts.get_contacts');

    Route::get($_route . '/contacts/get_available_options', [EmailMarketingController::class, 'getAvailableOptions'])
        ->name($_route . '.contacts.get_available_options');

    Route::delete($_route . '/contacts/{id}', [EmailMarketingController::class, 'deleteContact'])
        ->name($_route . '.contacts.delete');

    Route::post($_route . '/contacts/delete-selected', [EmailMarketingController::class, 'deleteSelectedContacts'])
        ->name($_route . '.contacts.delete-selected');

    Route::post($_route . '/contacts/save-contact', [EmailMarketingController::class, 'saveContact'])
        ->name($_route . '.contacts.save-contact');

    // New campaign routes
    Route::get($_route . '/campaign', [EmailMarketingController::class, 'campaign'])
        ->name($_route . '.campaign');

    Route::post($_route . '/campaign/send', [EmailMarketingController::class, 'sendCampaign'])
        ->name($_route . '.campaign.send');

    Route::post($_route . '/campaign/schedule', [EmailMarketingController::class, 'scheduleCampaign'])
        ->name($_route . '.campaign.schedule');

    Route::get($_route . '/campaign/{id}/stats', [EmailMarketingController::class, 'campaignStats'])
        ->name($_route . '.campaign.stats');

    Route::post($_route . '/campaign/email-preview', [EmailMarketingController::class, 'getCampaignEmailPreview'])
        ->name($_route . '.campaign.email-preview');

    Route::get($_route . '/campaign/{id}/recipients', [EmailMarketingController::class, 'getCampaignRecipients'])
        ->name($_route . '.campaign.recipients');

    Route::get($_route . '/campaign/{id}/states', [EmailMarketingController::class, 'getAllStates'])
        ->name($_route . '.campaign.states');

    Route::get($_route . '/campaign/test-email/{id}', [EmailMarketingController::class, 'testCampaignEmail'])
        ->name($_route . '.campaign.test-email');

});
