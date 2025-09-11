<?php

use App\Http\Middleware\UpdateLastLogin;
use Illuminate\Support\Facades\Route;

// Notification routes
Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::patch('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::patch('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::get('/by-type/{type}', [App\Http\Controllers\NotificationController::class, 'byType'])->name('notifications.by-type');
        Route::get('/types', [App\Http\Controllers\NotificationController::class, 'types'])->name('notifications.types');
        Route::post('/test', [App\Http\Controllers\NotificationController::class, 'createTest'])->name('notifications.create-test');
    });
});
