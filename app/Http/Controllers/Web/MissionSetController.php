<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MissionSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionSetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all available mission sets
        $missionSets = MissionSet::with(['missions'])
            ->orderBy('id')
            ->get();

        return view('mission-sets.index', compact('missionSets'));
    }

    public function show(MissionSet $missionSet)
    {
        $user = Auth::user();
        
        // Load mission set with daily missions
        $missionSet->load(['missions' => function ($query) {
            $query->orderBy('day_number');
        }]);

        // Check if user has completed any missions in this set
        $completedMissions = $user->missionCompletions()
            ->where('mission_set_id', $missionSet->id)
            ->pluck('daily_mission_id')
            ->toArray();

        return view('mission-sets.show', compact('missionSet', 'completedMissions'));
    }

    public function assign(Request $request, MissionSet $missionSet)
    {
        $user = Auth::user();
        
        // Assign the mission set to user
        $user->update([
            'active_mission_set_id' => $missionSet->id,
            'mission_started_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', __('mission_sets.assigned_successfully'));
    }
}
