<?php
use App\Http\Controllers\ReferralController;
use App\Http\Middleware\UpdateLastLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupportTicketController;

Route::middleware(['auth', 'user.type:admin', UpdateLastLogin::class])->group(function () {
    // Admin dashboard
    // Route::get('/admin', redirect()->to(url('/admin/index')))->name('admin');

    Route::redirect('/admin', '/admin/index')->name('admin');

    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats/{lastDays?}', [AdminController::class, 'getStats'])->name('admin.get_stats');

    // User management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::get('/admin/lead-sources', [AdminController::class, 'leadSources'])->name('admin.lead-sources');
    Route::patch('/admin/users/{id}/buy-box', [AdminController::class, 'updateUserBuyBox'])->name('admin.users.buy-box.update');
    Route::patch('/admin/users/{id}/email', [AdminController::class, 'updateUserEmail'])->name('admin.users.email.update');
    Route::patch('/admin/users/{id}/business', [AdminController::class, 'updateUserBusiness'])->name('admin.users.business.update');
    Route::post('/admin/users/{id}/business/logo', [AdminController::class, 'companyLogoUpload'])->name('admin.users.business.logo.upload');
    Route::post('/admin/users/{id}/profile-picture', [AdminController::class, 'profilePictureUpload'])->name('admin.users.profile-picture.upload');
    Route::delete('/admin/users/{id}/profile-picture', [AdminController::class, 'deleteProfilePicture'])->name('admin.users.profile-picture.delete');
    Route::put('/admin/users/{id}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.update-status');
    Route::post('/admin/users/{id}/login-as', [AdminController::class, 'loginAsUser'])->name('admin.users.login-as');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Property searches
    Route::get('/admin/property-searches', [AdminController::class, 'propertySearches'])->name('admin.property-searches');

    // Default values
    Route::get('/admin/default-values/upgrade-features', [AdminController::class, 'upgradeFeatures'])->name('admin.upgrade-features');
    Route::get('/admin/default-values/upgrade-features/{id}', [AdminController::class, 'showUpgradeFeature'])->name('admin.upgrade-features.show');
    Route::post('/admin/default-values/upgrade-features', [AdminController::class, 'storeUpgradeFeature'])->name('admin.upgrade-features.store');
    Route::put('/admin/default-values/upgrade-features/{id}', [AdminController::class, 'updateUpgradeFeature'])->name('admin.upgrade-features.update');
    Route::delete('/admin/default-values/upgrade-features/{id}', [AdminController::class, 'destroyUpgradeFeature'])->name('admin.upgrade-features.destroy');
    Route::get('/admin/default-values/county-states', [AdminController::class, 'countyStates'])->name('admin.county-states');
    Route::get('/admin/default-values/county-states/{id}', [AdminController::class, 'showCountyState'])->name('admin.county-states.show');
    Route::post('/admin/default-values/county-states', [AdminController::class, 'storeCountyState'])->name('admin.county-states.store');
    Route::put('/admin/default-values/county-states/{id}', [AdminController::class, 'updateCountyState'])->name('admin.county-states.update');
    Route::delete('/admin/default-values/county-states/{id}', [AdminController::class, 'destroyCountyState'])->name('admin.county-states.destroy');

    // Testimonials
    Route::get('/admin/default-values/testimonials', [AdminController::class, 'testimonials'])->name('admin.testimonials');
    Route::get('/admin/default-values/testimonials/{id}', [AdminController::class, 'showTestimonial'])->name('admin.testimonials.show');
    Route::post('/admin/default-values/testimonials', [AdminController::class, 'storeTestimonial'])->name('admin.testimonials.store');
    Route::put('/admin/default-values/testimonials/{id}', [AdminController::class, 'updateTestimonial'])->name('admin.testimonials.update');
    Route::delete('/admin/default-values/testimonials/{id}', [AdminController::class, 'destroyTestimonial'])->name('admin.testimonials.destroy');

    // Requests
    Route::get('/admin/requests/showing', [AdminController::class, 'showing'])->name('admin.requests.showing');
    Route::get('/admin/requests/ask-questions', [AdminController::class, 'askQuestions'])->name('admin.requests.ask-questions');
    Route::get('/admin/requests/contact-us', [AdminController::class, 'contactUsRequests'])->name('admin.requests.contact-us');
    Route::get('/admin/requests/instant-offers', [AdminController::class, 'instantOffers'])->name('admin.requests.instant-offers');
    Route::post('/admin/requests/send-email', [AdminController::class, 'sendEmail'])->name('admin.requests.send-email');

    // Property information
    Route::get('/admin/property-information', [AdminController::class, 'propertyInformation'])->name('admin.property-information');

    // Support tickets
    Route::get('/admin/support-tickets', [AdminController::class, 'supportTickets'])->name('admin.support-tickets');
    Route::get('/admin/support-tickets/{id}', [SupportTicketController::class, 'show'])->name('admin.support-tickets.show');
    Route::patch('/admin/support-tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('admin.support-tickets.update-status');
    Route::post('/admin/support-tickets/{id}/reply', [SupportTicketController::class, 'addReply'])->name('admin.support-tickets.reply');
    Route::patch('/admin/support-tickets/{id}/close', [SupportTicketController::class, 'close'])->name('admin.support-tickets.close');
    Route::patch('/admin/support-tickets/{id}/reopen', [SupportTicketController::class, 'reopen'])->name('admin.support-tickets.reopen');
    Route::get('/admin/support-tickets/categories', [SupportTicketController::class, 'getCategories'])->name('admin.support-tickets.categories');

    // HTTP Error Monitoring
    Route::get('/admin/http-errors', [AdminController::class, 'getHttpErrors'])->name('admin.http-errors');

    // Referrals
    Route::get('/admin/referrals/index', [ReferralController::class, 'adminReferralsIndex'])->name('admin.referrals.index');
    Route::get('/admin/referrals/w9s', [ReferralController::class, 'adminReferralsW9s'])->name('admin.referrals.w9s');
    Route::get('/admin/referrals/earnings', [ReferralController::class, 'adminReferralsEarnings'])->name('admin.referrals.earnings');
    Route::patch('/admin/referrals/w9s/approve', [ReferralController::class, 'approveW9'])->name('admin.referrals.w9s.approve');

    // Earnings management
    Route::patch('/admin/referrals/earnings/approve', [ReferralController::class, 'approveEarning'])->name('admin.referrals.earnings.approve');
    Route::patch('/admin/referrals/earnings/pay', [ReferralController::class, 'payEarning'])->name('admin.referrals.earnings.pay');
    Route::patch('/admin/referrals/earnings/bulk-approve', [ReferralController::class, 'bulkApproveEarnings'])->name('admin.referrals.earnings.bulk-approve');
    Route::patch('/admin/referrals/earnings/bulk-pay', [ReferralController::class, 'bulkPayEarnings'])->name('admin.referrals.earnings.bulk-pay');

    // Skip Trace Analytics
    Route::get('/admin/skip-trace-stats', [AdminController::class, 'skipTraceStats'])->name('admin.skip-trace-stats');
    Route::get('/admin/skip-trace-stats/{userId}', [AdminController::class, 'skipTraceUserDetail'])->name('admin.skip-trace-stats.user');
});
