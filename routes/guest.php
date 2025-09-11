<?php

use App\Http\Controllers\GuestController;
use App\Http\Middleware\UpdateLastLogin;



Route::get('/home', [GuestController::class, 'index'])
    ->name('guest.home');

Route::get('product_overview', [GuestController::class, 'productOverview'])
    ->name('guest.product_overview');

Route::get('deal_finder', [GuestController::class, 'dealFinder'])
    ->name('guest.deal_finder');


Route::get('market_coverage', [GuestController::class, 'marketCoverage'])
    ->name('guest.market_coverage');

Route::get('coverage/{county_slug}', [GuestController::class, 'coverage'])
    ->name('guest.coverage');


Route::get('my_buy_box', [GuestController::class, 'myBuyBox'])
    ->name('guest.my_buy_box');

Route::get('automated_offers', [GuestController::class, 'automatedOffers'])
    ->name('guest.automated_offers');

Route::get('fix_flip', [GuestController::class, 'fixFlip'])
    ->name('guest.fix_flip');

Route::get('buy_and_hold', [GuestController::class, 'buyAndHold'])
    ->name('guest.buy_and_hold');

Route::get('wholesalers', [GuestController::class, 'wholesalers'])
    ->name('guest.wholesalers');

Route::get('agents', [GuestController::class, 'agents'])
    ->name('guest.agents');

Route::get('property_ai', [GuestController::class, 'propertyAi'])
    ->name('guest.property_ai');

Route::get('data_solutions', [GuestController::class, 'dataSolutions'])
    ->name('guest.data_solutions');

Route::get('ai_onboarding', [GuestController::class, 'aiOnboarding'])
    ->name('guest.ai_onboarding');

Route::get('assessment_page', [GuestController::class, 'assessment'])
    ->name('guest.assessment_page');

Route::get('pricing', [GuestController::class, 'pricing'])
    ->name('guest.pricing');


Route::get('contact_us', [GuestController::class, 'contactUs'])
    ->name('guest.contact_us');

Route::post('contact_us', [GuestController::class, 'contactUsPost'])
    ->name('guest.contact_us.post');


// auth required
Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::get('instant_comp', [GuestController::class, 'instantComp'])
        ->name('guest.instant_comp');
});

// Assessment routes - accessible to both guests and authenticated users
Route::get('do_assessment', [App\Http\Controllers\AssessmentController::class, 'show'])
    ->name('guest.do_assessment');

Route::post('assessment/save-step', [App\Http\Controllers\AssessmentController::class, 'saveStep'])
    ->name('assessment.save_step');

Route::post('assessment/use-ai-for-smart-goals', [App\Http\Controllers\AssessmentController::class, 'useAiForSmartGoals'])
    ->name('assessment.use_ai_for_smart_goals');

Route::post('assessment/get-ai-overview', [App\Http\Controllers\AssessmentController::class, 'getAssessmentOverview'])
    ->name('assessment.get_ai_overview');

Route::post('assessment/get', [App\Http\Controllers\AssessmentController::class, 'getAssessment'])
    ->name('assessment.get');

Route::post('assessment/complete', [App\Http\Controllers\AssessmentController::class, 'completeAssessment'])
    ->name('assessment.complete');

Route::get('podcast', [GuestController::class, 'podcast'])
    ->name('guest.podcast');


// Route::get('guest/about', [GuestController::class, 'about'])
//     ->name('guest.about');

// Route::get('guest/contact', [GuestController::class, 'contact'])
//     ->name('guest.contact');

Route::get('privacy', [GuestController::class, 'privacy'])->name('guest.privacy');

Route::get('terms-of-use', [GuestController::class, 'termsofuse'])->name('guest.terms');
