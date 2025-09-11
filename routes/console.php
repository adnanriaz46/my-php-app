<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DispatchDueCampaigns;
use App\Console\Commands\DispatchAssessmentDripEmails;
use App\Console\Commands\CheckUserSubscriptions;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: process due campaigns every minute without overlap
Schedule::command(DispatchDueCampaigns::class)
    ->everyFifteenMinutes() // TODO: Every 15 minutes
    ->withoutOverlapping()
    ->onOneServer()
    // ->runInBackground()
;

// Schedule: send assessment drip emails once per day
Schedule::command(DispatchAssessmentDripEmails::class)
    ->dailyAt('18:30') // runs once every day at 18:30
    ->withoutOverlapping()
    ->onOneServer()
    ;

// Schedule: user subscription checker daily at 01:00
Schedule::command(CheckUserSubscriptions::class)
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer()
    ;
