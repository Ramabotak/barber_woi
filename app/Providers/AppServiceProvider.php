<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(\App\Models\Booking::class, \App\Policies\BookingPolicy::class);
        
        // Enable query logging hanya di development untuk detect N+1 queries
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log slow queries (>100ms)
                    logger()->warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }
    }
}
