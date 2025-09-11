<?php

use App\Http\Controllers\MlsOfferController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UpdateLastLogin;

Route::middleware(['auth', 'user.types:premium,admin', UpdateLastLogin::class])->group(function () {

    Route::get('mls-offer/pdf-templates', [MlsOfferController::class, 'index'])->name('mls-offer.pdf-templates');
    Route::post('mls-offer/pdf-templates', [MlsOfferController::class, 'store'])->name('mls-offer.pdf-templates.store');
    Route::put('mls-offer/pdf-templates/{id}', [MlsOfferController::class, 'update'])->name('mls-offer.pdf-templates.update');
    Route::delete('mls-offer/pdf-templates/{id}', [MlsOfferController::class, 'destroy'])->name('mls-offer.pdf-templates.destroy');
    Route::get('mls-offer/pdf-templates/{id}/download', [MlsOfferController::class, 'downloadTemplate'])->name('mls-offer.pdf-templates.download');
    Route::get('mls-offer/pdf-templates/{id}/fields', [MlsOfferController::class, 'listFields'])->name('mls-offer.pdf-templates.fields');

    Route::get('mls-offer/email-templates', [MlsOfferController::class, 'emailTemplates'])->name('mls-offer.email-templates');
    Route::post('mls-offer/email-templates', [MlsOfferController::class, 'storeEmailTemplate'])->name('mls-offer.email-templates.store');
    Route::put('mls-offer/email-templates/{id}', [MlsOfferController::class, 'updateEmailTemplate'])->name('mls-offer.email-templates.update');
    Route::delete('mls-offer/email-templates/{id}', [MlsOfferController::class, 'destroyEmailTemplate'])->name('mls-offer.email-templates.destroy');

    Route::get('mls-offer/mail-logs', [MlsOfferController::class, 'generatedLogs'])->name('mls-offer.mail-logs');

});
