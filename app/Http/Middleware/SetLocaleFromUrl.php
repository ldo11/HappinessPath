<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api') || $request->is('api/*')) {
            return $next($request);
        }

        $routeLocale = $request->route('locale');
        $segmentLocale = $request->segment(1);
        $supportedLocales = ['en', 'vi', 'de', 'kr'];

        $preferredLocale = (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
            ?? session('locale')
            ?? config('app.locale', 'en');

        $preferredLocale = in_array($preferredLocale, $supportedLocales, true) ? $preferredLocale : 'en';

        if (! $routeLocale) {
            if (in_array($segmentLocale, $supportedLocales, true)) {
                session(['locale' => $segmentLocale]);
                app()->setLocale($segmentLocale);
                URL::defaults(['locale' => $segmentLocale]);

                return $next($request);
            }

            $path = ltrim($request->path(), '/');
            $path = $path === '' ? '' : '/'.$path;

            return redirect('/'.$preferredLocale.$path);
        }

        if (! in_array($routeLocale, $supportedLocales, true)) {
            abort(404);
        }

        // If user is authenticated, their profile language is authoritative.
        if (Auth::check() && is_string($preferredLocale) && $preferredLocale !== $routeLocale) {
            $path = $request->path();
            $segments = array_values(array_filter(explode('/', $path)));
            if (count($segments) > 0 && in_array($segments[0], $supportedLocales, true)) {
                $segments[0] = $preferredLocale;
                session(['locale' => $preferredLocale]);
                app()->setLocale($preferredLocale);
                URL::defaults(['locale' => $preferredLocale]);
                return redirect('/' . implode('/', $segments));
            }
        }

        session(['locale' => $routeLocale]);
        app()->setLocale($routeLocale);

        URL::defaults(['locale' => $routeLocale]);

        return $next($request);
    }
}
