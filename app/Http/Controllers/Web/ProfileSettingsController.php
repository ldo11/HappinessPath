<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ProfileSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
            'language' => ['required', 'in:vi,en,de,kr'],
            'religion' => ['nullable', 'in:buddhism,christianity,science,none'],
        ]);

        $service->update($request->user(), $data);

        $language = (string) $data['language'];
        session(['locale' => $language]);
        app()->setLocale($language);
        URL::defaults(['locale' => $language]);

        return redirect()->route('profile.settings.edit');
    }
}
