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
        $supportedLocales = (array) config('app.supported_locales', ['en', 'vi', 'de', 'kr']);
        if ($supportedLocales === []) {
            $supportedLocales = ['en', 'vi', 'de', 'kr'];
        }

        $preferredLocale = (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
            ?? session('locale')
            ?? config('app.locale', 'en');

        $preferredLocale = in_array($preferredLocale, $supportedLocales, true) ? $preferredLocale : 'en';

        // Check for multiple locale prefixes (e.g., /en/en/something)
        $path = $request->path();
        $segments = array_values(array_filter(explode('/', $path)));
        
        // If we detect multiple locale prefixes, redirect to clean URL
        $localePrefixCount = 0;
        foreach ($segments as $segment) {
            if (in_array($segment, $supportedLocales, true)) {
                $localePrefixCount++;
            } else {
                break;
            }
        }
        
        if ($localePrefixCount > 1) {
            // Remove all locale prefixes and redirect with single preferred locale
            $cleanPath = implode('/', array_slice($segments, $localePrefixCount));
            $cleanPath = $cleanPath === '' ? '' : '/' . $cleanPath;
            $targetUrl = '/' . $preferredLocale . $cleanPath;
            
            // Prevent infinite redirect loop and exclude logout paths
            if ($request->fullUrl() === $targetUrl || 
                $request->path() === ltrim($targetUrl, '/') ||
                str_contains($request->path(), 'logout')) {
                return $next($request);
            }
            
            return redirect($targetUrl);
        }

        if (! $routeLocale) {
            if (in_array($segmentLocale, $supportedLocales, true)) {
                session(['locale' => $segmentLocale]);
                app()->setLocale($segmentLocale);
                URL::defaults(['locale' => $segmentLocale]);

                return $next($request);
            }

            $path = ltrim($request->path(), '/');
            $path = $path === '' ? '' : '/'.$path;
            $targetUrl = '/'.$preferredLocale.$path;
            
            // Prevent infinite redirect loop and exclude logout paths
            if ($request->fullUrl() === $targetUrl || 
                $request->path() === ltrim($targetUrl, '/') ||
                str_contains($request->path(), 'logout')) {
                return $next($request);
            }

            return redirect($targetUrl);
        }

        if (! in_array($routeLocale, $supportedLocales, true)) {
            abort(404);
        }

        // If user is authenticated, their profile language is authoritative.
        // However, allow manual locale selection via URL for better UX
        if (Auth::check() && is_string($preferredLocale) && $preferredLocale !== $routeLocale) {
            // Check if user is manually accessing a different locale via URL
            // If so, respect their manual choice and don't redirect
            $path = $request->path();
            $segments = array_values(array_filter(explode('/', $path)));
            
            // If the first segment is a valid locale and matches the route locale,
            // it means user manually typed this URL, so respect their choice
            if (count($segments) > 0 && in_array($segments[0], $supportedLocales, true) && $segments[0] === $routeLocale) {
                // User manually selected this locale, update their session to match
                session(['locale' => $routeLocale]);
                app()->setLocale($routeLocale);
                URL::defaults(['locale' => $routeLocale]);
                // Don't redirect - let them use their manually chosen locale
            } else {
                // Otherwise, redirect to their preferred locale
                if (count($segments) > 0 && in_array($segments[0], $supportedLocales, true)) {
                    if ($segments[0] !== $preferredLocale) {
                        $segments[0] = $preferredLocale;
                        session(['locale' => $preferredLocale]);
                        app()->setLocale($preferredLocale);
                        URL::defaults(['locale' => $preferredLocale]);
                        $targetUrl = '/' . implode('/', $segments);
                        
                        // Prevent infinite redirect loop and exclude logout paths
                        if ($request->fullUrl() === $targetUrl || 
                            $request->path() === ltrim($targetUrl, '/') ||
                            str_contains($request->path(), 'logout')) {
                            return $next($request);
                        }
                        
                        return redirect($targetUrl);
                    }
                }
            }
        }

        session(['locale' => $routeLocale]);
        app()->setLocale($routeLocale);

        URL::defaults(['locale' => $routeLocale]);

        return $next($request);
    }
}
