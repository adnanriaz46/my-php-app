<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\UpdateLastLogin;
Route::get('/test', function () {
//    phpinfo();
//    \Illuminate\Support\Facades\Mail::send([], [], function ($message) {
//        $message->to('arammarm@gmail.com')
//            ->subject('This is just a test message')
//            ->html('<div>Yes this is HTML element</div>');
//    });
});

Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    // Basic
    Route::redirect('settings', url('/settings/profile'));
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('settings/profile/upload', [ProfileController::class, 'avatarUpload'])->name('profile.upload');
//    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Professional
    Route::get('settings/profile/professional', [ProfileController::class, 'professionalEdit'])->name('profile.professional.edit');
    Route::patch('settings/profile/professional', [ProfileController::class, 'professionalUpdate'])->name('profile.professional.update');
    Route::post('settings/profile/professional/upload', [ProfileController::class, 'companyLogoUpload'])->name('profile.professional.upload');

    // Email Settings
    Route::get('settings/profile/email', [ProfileController::class, 'emailEdit'])->name('profile.email.edit');
    Route::patch('settings/profile/email', [ProfileController::class, 'emailUpdate'])->name('profile.email.update');
    Route::post('settings/profile/company-email-verification', [ProfileController::class, 'sendCompanyEmailVerification'])->name('profile.company-email.verification.send');

    // Buy Box
    Route::get('settings/profile/buy_box', [ProfileController::class, 'buyBoxEdit'])->name('profile.buy_box.edit');
    Route::patch('settings/profile/buy_box', [ProfileController::class, 'buyBoxUpdate'])->name('profile.buy_box.update');

    // Password
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/upload/assets', function () {
        return Inertia::render('settings/UploadAssets');
    });
    Route::post('settings/upload/assets', [ProfileController::class, 'assetsUpload'])->name('assets.upload');

    Route::post('settings/subscription/create', [PaymentController::class, 'createSubscription'])->name('subscription.create');
    Route::get('settings/subscription/failed', [PaymentController::class, 'failedResponse'])->name('subscription.failed');
    Route::get('settings/subscription/success', [PaymentController::class, 'successResponse'])->name('subscription.success');
    Route::post('settings/subscription/cancel', [PaymentController::class, 'cancelSubscription'])->name('subscription.cancel');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});

Route::post('settings/subscription/webook', [PaymentController::class, 'webHookHandling'])->name('subscription.webhook');
