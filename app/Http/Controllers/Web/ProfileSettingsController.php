<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use App\Services\ProfileSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ProfileSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $painPoints = collect();
        $selectedConsultantPainPointIds = [];

        if ($user->hasRole('consultant')) {
            $painPoints = PainPoint::query()->orderBy('name')->get();
            $selectedConsultantPainPointIds = $user->consultantPainPoints()->pluck('pain_points.id')->all();
        }

        return view('profile.settings', [
            'user' => $user,
            'painPoints' => $painPoints,
            'selectedConsultantPainPointIds' => $selectedConsultantPainPointIds,
        ]);
    }

    public function update(Request $request, ProfileSettingsService $service)
    {
        $data = $request->validate([
            'geo_privacy' => ['required', 'boolean'],
            'spiritual_preference' => ['required', 'in:buddhism,christianity,secular'],
            'language' => ['required', 'in:vi,en,de,kr'],
            'religion' => ['nullable', 'in:buddhism,christianity,science,none'],
            'is_available' => ['nullable', 'boolean'],
            'consultant_pain_points' => ['nullable', 'array'],
            'consultant_pain_points.*' => ['integer', 'exists:pain_points,id'],
        ]);

        $service->update($request->user(), $data);

        $language = (string) $data['language'];
        session(['locale' => $language]);
        app()->setLocale($language);
        URL::defaults(['locale' => $language]);

        return redirect()->route('profile.settings.edit');
    }
}
