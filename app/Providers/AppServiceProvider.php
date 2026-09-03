<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Register Model Observers
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);

        // Query logging for development
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                // Log slow queries (over 500ms)
                if ($query->time > 500) {
                    \Illuminate\Support\Facades\Log::warning('Slow Query: ' . $query->sql . ' (' . $query->time . 'ms)');
                }
            });
        }

        // Register macro for cached active interns
        Cache::macro('activeInterns', function ($limit = 50) {
            return Cache::remember('active-interns-' . $limit, 3600, function () use ($limit) {
                return \App\Models\Intern::with('user')
                    ->where('status', 'active')
                    ->limit($limit)
                    ->get();
            });
        });
    }
}
