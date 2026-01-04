<?php

namespace App\Providers;

use App\Models\Solution;
use App\Models\User;
use App\Observers\SolutionObserver;
use App\Observers\UserObserver;
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
        User::observe(UserObserver::class);
    }
}
