<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMission;
use Illuminate\Http\Request;

class DailyMissionController extends Controller
{
    private function redirectToIndex(Request $request)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.daily-missions.index', ['locale' => app()->getLocale()]);
        }

        return redirect()->route('admin.daily-missions.index');
    }

    public function index()
    {
        $missions = DailyMission::query()
            ->with('createdBy')
            ->latest('id')
            ->paginate(20);

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.daily-missions.index', compact('missions'));
        }

        return view('admin.daily-missions.index', compact('missions'));
    }

    public function create()
    {
        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.daily-missions.create');
        }

        return view('admin.daily-missions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'is_body' => ['nullable', 'boolean'],
            'is_mind' => ['nullable', 'boolean'],
            'is_wisdom' => ['nullable', 'boolean'],
        ]);

        DailyMission::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'points' => (int) $data['points'],
            'is_body' => (bool) ($data['is_body'] ?? false),
            'is_mind' => (bool) ($data['is_mind'] ?? false),
            'is_wisdom' => (bool) ($data['is_wisdom'] ?? false),
            'created_by_id' => $request->user()->id,
        ]);

        return $this->redirectToIndex($request);
    }

    public function edit(DailyMission $dailyMission)
    {
        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.daily-missions.edit', [
                'mission' => $dailyMission,
            ]);
        }

        return view('admin.daily-missions.edit', [
            'mission' => $dailyMission,
        ]);
    }

    public function update(Request $request, DailyMission $dailyMission)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'is_body' => ['nullable', 'boolean'],
            'is_mind' => ['nullable', 'boolean'],
            'is_wisdom' => ['nullable', 'boolean'],
        ]);

        $dailyMission->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'points' => (int) $data['points'],
            'is_body' => (bool) ($data['is_body'] ?? false),
            'is_mind' => (bool) ($data['is_mind'] ?? false),
            'is_wisdom' => (bool) ($data['is_wisdom'] ?? false),
        ]);

        return $this->redirectToIndex($request);
    }

    public function destroy(DailyMission $dailyMission)
    {
        $dailyMission->delete();

        return $this->redirectToIndex(request());
    }
}
