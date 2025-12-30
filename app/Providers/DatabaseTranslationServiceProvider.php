<?php

namespace App\Providers;

use App\Translation\DatabaseTranslationLoader;
use Illuminate\Support\ServiceProvider;

class DatabaseTranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new DatabaseTranslationLoader($loader);
        });
    }
}
