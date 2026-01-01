<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        $canonicalRole = $user->role;
        $roleLower = is_string($canonicalRole) ? strtolower($canonicalRole) : $canonicalRole;
        $effectiveRole = match ($roleLower) {
            null, '' => 'user',
            'member' => 'user',
            'volunteer' => 'translator',
            default => $roleLower,
        };

        if ($effectiveRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($effectiveRole === 'translator') {
            return redirect()->route('user.translator.translator.dashboard');
        }

        if ($effectiveRole === 'consultant') {
            return redirect()->route('consultant.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/en/login');
    }
}
