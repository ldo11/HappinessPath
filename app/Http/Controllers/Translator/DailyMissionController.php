<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\DailyMission;
use Illuminate\Http\Request;

class DailyMissionController extends Controller
{
    public function index()
    {
        $missions = DailyMission::query()
            ->with('createdBy')
            ->latest('id')
            ->paginate(20);

        return view('translator.daily-missions.index', compact('missions'));
    }

    public function show($locale, $dailyMissionId)
    {
        $dailyMission = DailyMission::findOrFail($dailyMissionId);

        return view('translator.daily-missions.show', [
            'mission' => $dailyMission,
        ]);
    }

    public function update(Request $request, $locale, $dailyMissionId)
    {
        $dailyMission = DailyMission::findOrFail($dailyMissionId);

        $data = $request->validate([
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.vi' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.vi' => ['nullable', 'string'],
        ]);

        foreach (['en', 'vi'] as $locale) {
            if (array_key_exists('title', $data) && array_key_exists($locale, (array) $data['title'])) {
                $dailyMission->setTranslation('title', $locale, (string) ($data['title'][$locale] ?? ''));
            }
            if (array_key_exists('description', $data) && array_key_exists($locale, (array) $data['description'])) {
                $dailyMission->setTranslation('description', $locale, (string) ($data['description'][$locale] ?? ''));
            }
        }

        $dailyMission->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $dailyMission->id,
                'title' => $dailyMission->getTranslations('title'),
                'description' => $dailyMission->getTranslations('description'),
            ]);
        }

        return redirect()
            ->route('translator.daily-missions.show', $dailyMission)
            ->with('success', 'Daily mission translation saved.');
    }
}
