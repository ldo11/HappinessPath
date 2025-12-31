<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands()
    ->withMiddleware(function (Middleware $middleware) {
        if (env('APP_ENV') === 'testing') {
            $middleware->validateCsrfTokens(except: ['*']);
        } else {
            $middleware->validateCsrfTokens(except: [
                '*/videos/*/claim',
            ]);
        }
        $middleware->web(append: [\App\Http\Middleware\SetLocaleFromUrl::class]);
        
        // Use custom Authenticate middleware that handles locale redirects
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'user' => \App\Http\Middleware\UserMiddleware::class,
            'translator' => \App\Http\Middleware\TranslatorMiddleware::class,
            'setLocaleFromUrl' => \App\Http\Middleware\SetLocaleFromUrl::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
