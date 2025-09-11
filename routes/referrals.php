<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferralController;

Route::middleware(['auth', \App\Http\Middleware\UpdateLastLogin::class])->group(function () {
    $_route = 'referrals';
    Route::get($_route, [ReferralController::class, 'index'])
        ->name($_route);

    Route::post('upload-w9', [ReferralController::class, 'uploadW9'])
        ->name('referral.upload-w9');
});

Route::get('ref/{uuid}', [ReferralController::class, 'referralLink'])
    ->name('referral-link');