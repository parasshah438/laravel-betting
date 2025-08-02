<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Set the default string length for database columns to avoid issues with older MySQL versions
        Schema::defaultStringLength(191);

        // Optionally, you can add other bootstrapping logic here
        // For example, you might want to load routes, views, or configurations
    }
}
