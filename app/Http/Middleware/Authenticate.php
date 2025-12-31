<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Get the current locale from the request or session
        $locale = $request->route('locale') 
            ?? session('locale') 
            ?? config('app.locale', 'en');
        
        return route('login', ['locale' => $locale]);
    }
}
