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
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'consultant_pain_points' => ['nullable', 'array'],
            'consultant_pain_points.*' => ['integer', 'exists:pain_points,id'],
        ]);

        $service->update($request->user(), $data);

        $language = (string) ($request->user()->language ?? session('locale') ?? config('app.locale', 'en'));
        session(['locale' => $language]);
        app()->setLocale($language);
        URL::defaults(['locale' => $language]);

        return redirect()->route('user.profile.settings.edit', ['locale' => app()->getLocale()]);
    }
}
