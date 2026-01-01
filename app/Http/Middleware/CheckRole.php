<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage: ->middleware('role:admin') or ->middleware('role:translator,consultant')
     * Admin always passes.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized.');
        }

        $userRole = Auth::user()->role;
        $roleLower = is_string($userRole) ? strtolower($userRole) : $userRole;
        $effectiveRole = match ($roleLower) {
            null, '' => 'user',
            'member' => 'user',
            'volunteer' => 'translator',
            default => $roleLower,
        };

        if ($effectiveRole === 'admin') {
            return $next($request);
        }

        $allowed = [];
        foreach ($roles as $r) {
            if (!is_string($r)) {
                continue;
            }

            foreach (preg_split('/[|,]/', $r) ?: [] as $part) {
                $part = strtolower(trim($part));
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        $allowed = array_values(array_unique($allowed));
        if ($allowed === []) {
            return $next($request);
        }

        if (!in_array($effectiveRole, $allowed, true)) {
            abort(403, 'Unauthorized access. Role required.');
        }

        return $next($request);
    }
}
