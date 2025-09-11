<?php

use App\Http\Middleware\UpdateLastLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportTicketController;
use Inertia\Inertia;

Route::middleware(['auth', UpdateLastLogin::class])->group(function () {
    Route::get('/support/create', function () {
        return Inertia::render('support-tickets/CreateTicket');
    })->name('support.create');

    Route::post('/support/tickets', [SupportTicketController::class, 'store'])->name('support.tickets.store');
    Route::get('/support/tickets', [SupportTicketController::class, 'userTickets'])->name('support.tickets.index');
    Route::get('/support/tickets/{id}', [SupportTicketController::class, 'userShow'])->name('support.tickets.show');
    Route::post('/support/tickets/{id}/reply', [SupportTicketController::class, 'userReply'])->name('support.tickets.reply');
    Route::patch('/support/tickets/{id}/close', [SupportTicketController::class, 'userClose'])->name('support.tickets.close');
});
