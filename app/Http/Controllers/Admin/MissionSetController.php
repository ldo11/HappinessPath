<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMission;
use App\Models\MissionSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionSetController extends Controller
{
    private function resolveMissionSet()
    {
        $id = request()->route('missionSet');
        
        if ($id instanceof MissionSet) {
            return $id;
        }
        
        return MissionSet::findOrFail($id);
    }

    private function redirectToIndex(Request $request)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.mission-sets.index', ['locale' => app()->getLocale()]);
        }

        return redirect()->route('admin.mission-sets.index');
    }

    private function redirectToShow(Request $request, MissionSet $missionSet)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.mission-sets.show', [
                'locale' => app()->getLocale(),
                'missionSet' => $missionSet->id,
            ]);
        }

        return redirect()->route('admin.mission-sets.show', $missionSet);
    }

    public function index()
    {
        $missionSets = MissionSet::with('creator')
            ->withCount('missions')
            ->latest()
            ->paginate(20);

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.mission-sets.index', compact('missionSets'));
        }

        return view('admin.mission-sets.index', compact('missionSets'));
    }

    public function create()
    {
        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.mission-sets.create');
        }

        return view('admin.mission-sets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['is_default'])) {
            // Unset other defaults
            MissionSet::where('is_default', true)->update(['is_default' => false]);
        }

        $missionSet = MissionSet::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'standard',
            'is_default' => $data['is_default'] ?? false,
            'created_by' => Auth::id(),
        ]);

        return $this->redirectToShow($request, $missionSet);
    }

    public function show()
    {
        $missionSet = $this->resolveMissionSet();
        $missionSet->load(['missions' => function ($query) {
            $query->orderBy('day_number');
        }]);

        $availableMissions = DailyMission::query()
            ->select('id', 'title', 'points', 'is_body', 'is_mind', 'is_wisdom') // Optimize selection
            ->orderBy('title')
            ->get();

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.mission-sets.show', compact('missionSet', 'availableMissions'));
        }

        return view('admin.mission-sets.show', compact('missionSet', 'availableMissions'));
    }

    public function cloneMission(Request $request)
    {
        $missionSet = $this->resolveMissionSet();
        
        $data = $request->validate([
            'source_mission_id' => ['required', 'exists:daily_missions,id'],
            'day_number' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $sourceMission = DailyMission::findOrFail($data['source_mission_id']);
        
        // Replicate the mission
        $newMission = $sourceMission->replicate();
        $newMission->mission_set_id = $missionSet->id;
        $newMission->day_number = $data['day_number'];
        $newMission->created_by_id = Auth::id();
        $newMission->push(); // Save the replicated model

        return $this->redirectToShow($request, $missionSet)
            ->with('success', 'Mission added successfully!');
    }

    public function edit()
    {
        $missionSet = $this->resolveMissionSet();

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.mission-sets.edit', compact('missionSet'));
        }

        return view('admin.mission-sets.edit', compact('missionSet'));
    }

    public function update(Request $request)
    {
        $missionSet = $this->resolveMissionSet();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['is_default'])) {
            // Unset other defaults
            MissionSet::where('id', '!=', $missionSet->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $missionSet->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'standard',
            'is_default' => $data['is_default'] ?? false,
        ]);

        return $this->redirectToShow($request, $missionSet);
    }

    public function destroy()
    {
        $missionSet = $this->resolveMissionSet();
        // Determine redirect before deleting
        $redirect = $this->redirectToIndex(request());
        
        $missionSet->delete();

        return $redirect;
    }
}
