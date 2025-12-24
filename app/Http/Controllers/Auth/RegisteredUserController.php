<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'city' => $data['city'],
            'spiritual_preference' => $data['spiritual_preference'],
            'geo_privacy' => true,
            'onboarding_status' => 'new', // New users start with assessment
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on onboarding status
        return redirect()->route('assessment');
    }
}
