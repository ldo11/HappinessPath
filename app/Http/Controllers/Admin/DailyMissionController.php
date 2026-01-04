<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'mission_set_id' => ['nullable', 'exists:mission_sets,id'],
            'day_number' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        DailyMission::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'points' => (int) $data['points'],
            'is_body' => (bool) ($data['is_body'] ?? false),
            'is_mind' => (bool) ($data['is_mind'] ?? false),
            'is_wisdom' => (bool) ($data['is_wisdom'] ?? false),
            'mission_set_id' => $data['mission_set_id'] ?? null,
            'day_number' => $data['day_number'] ?? null,
            'created_by_id' => $request->user()->id,
        ]);

        if (!empty($data['mission_set_id'])) {
             $routeName = (string) optional($request->route())->getName();
             if (str_starts_with($routeName, 'consultant.')) {
                 return redirect()->route('consultant.mission-sets.show', ['locale' => app()->getLocale(), 'missionSet' => $data['mission_set_id']]);
             }
             return redirect()->route('admin.mission-sets.show', $data['mission_set_id']);
        }

        return $this->redirectToIndex($request);
    }

    public function edit()
    {
        $dailyMission = $this->resolveDailyMission();
        
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

    public function update(Request $request)
    {
        $dailyMission = $this->resolveDailyMission();
        
        $routeName = (string) optional($request->route())->getName();
        if (str_starts_with($routeName, 'consultant.') && (int) $dailyMission->created_by_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'is_body' => ['nullable', 'boolean'],
            'is_mind' => ['nullable', 'boolean'],
            'is_wisdom' => ['nullable', 'boolean'],
            'mission_set_id' => ['nullable', 'exists:mission_sets,id'],
            'day_number' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $dailyMission->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'points' => (int) $data['points'],
            'is_body' => (bool) ($data['is_body'] ?? false),
            'is_mind' => (bool) ($data['is_mind'] ?? false),
            'is_wisdom' => (bool) ($data['is_wisdom'] ?? false),
            'mission_set_id' => $data['mission_set_id'] ?? $dailyMission->mission_set_id,
            'day_number' => $data['day_number'] ?? $dailyMission->day_number,
        ]);

        if ($dailyMission->mission_set_id) {
             $routeName = (string) optional($request->route())->getName();
             if (str_starts_with($routeName, 'consultant.')) {
                 return redirect()->route('consultant.mission-sets.show', ['locale' => app()->getLocale(), 'missionSet' => $dailyMission->mission_set_id]);
             }
             return redirect()->route('admin.mission-sets.show', $dailyMission->mission_set_id);
        }

        return $this->redirectToIndex($request);
    }

    public function destroy(Request $request)
    {
        $dailyMission = $this->resolveDailyMission();
        
        $routeName = (string) optional($request->route())->getName();
        if (str_starts_with($routeName, 'consultant.') && (int) $dailyMission->created_by_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $missionSetId = $dailyMission->mission_set_id;
        
        $dailyMission->delete();

        if ($missionSetId) {
             $routeName = (string) optional(request()->route())->getName();
             if (str_starts_with($routeName, 'consultant.')) {
                 return redirect()->route('consultant.mission-sets.show', ['locale' => app()->getLocale(), 'missionSet' => $missionSetId]);
             }
             return redirect()->route('admin.mission-sets.show', $missionSetId);
        }

        return $this->redirectToIndex(request());
    }

    private function resolveDailyMission()
    {
        $id = request()->route('dailyMission');
        
        if ($id instanceof DailyMission) {
            return $id;
        }
        
        return DailyMission::findOrFail($id);
    }
}
