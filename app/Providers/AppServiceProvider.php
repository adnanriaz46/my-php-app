<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!extension_loaded('imagick')) {
            // Stop the app from starting and show a clear error
            exit("❌ Imagick PHP extension is not installed or enabled.\n");
        }

    }
}
