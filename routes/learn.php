<?php
use App\Http\Middleware\UpdateLastLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LearnController;

Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::get('/learn/{slug?}', [LearnController::class, 'index'])->name('learn.index');
    Route::get('/learn/get-data/{id}', [LearnController::class, 'getData'])->name('learn.getData');
    Route::post('/learn/complete-video/{videoId}', [LearnController::class, 'completeVideo'])->name('learn.completeVideo');
});
