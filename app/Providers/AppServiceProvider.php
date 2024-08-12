<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

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
        // uncomment to log all the queries
        DB::listen(function ($query) {
            \Log::info($query->sql, ['bindings' => $query->bindings, 'time' => $query->time]);
        });
    }
}
