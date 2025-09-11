<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyerSearchController;
use App\Http\Controllers\BuyerSkipTraceController;

Route::middleware(['auth', \App\Http\Middleware\UpdateLastLogin::class])->group(function () {
    Route::get('buyers/search', [BuyerSearchController::class, 'index'])->name('buyers.search');
    Route::get('buyers/skip-tracing', [BuyerSkipTraceController::class, 'index'])->name('buyers.skip_tracing');
Route::post('buyers/skip-tracing/{listId}', [BuyerSkipTraceController::class, 'skipTraceList'])->name('buyers.skip_tracing.run');
Route::get('buyers/api/search-with-skip-trace', [BuyerSkipTraceController::class, 'searchBuyersWithSkipTrace'])->name('buyers.api.search_with_skip_trace');
});