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

        // Prioritize user's preferred display language
        $locale = $user->display_language ?? $user->language ?? $user->locale ?? app()->getLocale();
        
        // Ensure locale is supported
        if (!in_array($locale, ['en', 'vi', 'de', 'kr'])) {
            $locale = 'en';
        }

        // Update session to match user preference
        session(['locale' => $locale]);
        app()->setLocale($locale);

        $role = $user->role;

        if ($role === 'admin') {
            return redirect('/' . $locale . '/admin/dashboard');
        }

        if ($role === 'translator') {
            return redirect('/' . $locale . '/translator/dashboard');
        }

        if ($role === 'consultant') {
            return redirect('/' . $locale . '/consultant/dashboard');
        }

        return redirect('/' . $locale . '/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $locale = (string) ($request->route('locale')
            ?? $request->get('locale')
            ?? session('locale')
            ?? config('app.locale', 'en'));

        return redirect('/' . $locale . '/login');
    }
}
