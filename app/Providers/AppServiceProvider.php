<?php

namespace App\Providers;

use App\Models\Solution;
use App\Observers\SolutionObserver;
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
        Solution::observe(SolutionObserver::class);
    }
}
