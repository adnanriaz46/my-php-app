<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfTemplateController;
use App\Http\Controllers\GeneratedPdfController;




use App\Http\Controllers\Api\WholesalePropertyApiController;

// Wholesale Property API Routes
Route::prefix('v1/wholesale')->middleware(['api.key'])->group(function () {
    Route::post('/templates/upload', [PdfTemplateController::class, 'upload']);
Route::post('/templates/{id}/fields', [PdfTemplateController::class, 'saveFields']);
Route::get('/templates', [PdfTemplateController::class, 'index']);
Route::post('/templates/{id}/generate', [GeneratedPdfController::class, 'generate']);
Route::get('/generated', [GeneratedPdfController::class, 'index']); 
    // Get all properties with filters and pagination
    Route::get('/properties', [WholesalePropertyApiController::class, 'index'])
        ->name('api.wholesale.properties.index');
    
    // Get specific property by ID
    Route::get('/properties/{id}', [WholesalePropertyApiController::class, 'show'])
        ->name('api.wholesale.properties.show')
        ->where('id', '[0-9]+');
    
    // Get property by slug
    Route::get('/properties/slug/{slug}', [WholesalePropertyApiController::class, 'showBySlug'])
        ->name('api.wholesale.properties.show_by_slug');
    
    // Search properties
    Route::get('/properties/search', [WholesalePropertyApiController::class, 'search'])
        ->name('api.wholesale.properties.search');
    
    // Get available counties
    Route::get('/counties', [WholesalePropertyApiController::class, 'counties'])
        ->name('api.wholesale.counties');
    
    // Get cities for a county
    Route::get('/cities', [WholesalePropertyApiController::class, 'cities'])
        ->name('api.wholesale.cities');
    
    // Get statistics
    Route::get('/stats', [WholesalePropertyApiController::class, 'stats'])
        ->name('api.wholesale.stats');
});
