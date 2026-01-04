<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Http\Request;

class UserProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $missionSets = MissionSet::orderBy('name')->get();

        return view('consultant.users.index', compact('users', 'missionSets'));
    }

    public function show(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Ensure user belongs to consultant logic if needed, 
        // but typically consultant can view any user's progress or search for them.
        
        $user->load(['activeMissionSet.missions']);
        
        // Calculate current progress
        $currentMission = null;
        if ($user->activeMissionSet && $user->mission_started_at) {
            $daysSinceStart = $user->mission_started_at->diffInDays(now()) + 1;
            // Cap at 30 days or handle rollover? For now, just show current day.
            $currentDay = (int) $daysSinceStart;
            
            $currentMission = $user->activeMissionSet->missions()
                ->where('day_number', $currentDay)
                ->first();
        }

        $missionSets = MissionSet::orderBy('name')->get();

        return view('consultant.users.progress', compact('user', 'currentMission', 'missionSets', 'currentDay'));
    }

    public function assign(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $data = $request->validate([
            'mission_set_id' => ['required', 'exists:mission_sets,id'],
            'reset_progress' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'active_mission_set_id' => $data['mission_set_id'],
        ];

        if (!empty($data['reset_progress'])) {
            $updateData['mission_started_at'] = now();
        }

        $user->update($updateData);

        return back()->with('success', 'Mission set assigned successfully!');
    }
}
