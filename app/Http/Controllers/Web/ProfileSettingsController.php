<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ProfileSettingsService;
use Illuminate\Http\Request;

class ProfileSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, ProfileSettingsService $service)
    {
        $data = $request->validate([
            'geo_privacy' => ['required', 'boolean'],
            'spiritual_preference' => ['required', 'in:buddhism,christianity,secular'],
        ]);

        $service->update($request->user(), $data);

        return redirect()->route('profile.settings.edit');
    }
}
