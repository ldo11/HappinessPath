<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MissionSetController extends Controller
{
    /**
     * Display a listing of the mission sets.
     */
    public function index()
    {
        $missionSets = MissionSet::with('creator')->latest()->get();
        return view('consultant.mission_sets.index', compact('missionSets'));
    }

    /**
     * Show the form for creating a new mission set.
     */
    public function create()
    {
        return view('consultant.mission_sets.create');
    }

    /**
     * Store a newly created mission set in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en' => 'required|string',
            'description.en' => 'nullable|string',
            'type' => 'required|in:healing,growth,mindfulness',
        ]);

        $missionSet = MissionSet::create([
            'name' => ['en' => $validated['name']['en']], // Extend to other locales if UI supports
            'description' => ['en' => $validated['description']['en'] ?? ''],
            'type' => $validated['type'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('user.admin.mission-sets.show', [
            'locale' => app()->getLocale(),
            'mission_set' => $missionSet->id // Explicitly use the parameter name expected by resource route
        ])
        ->with('success', 'Mission Set created successfully.');
    }

    /**
     * Display the specified mission set.
     */
    public function show(MissionSet $missionSet)
    {
        $missionSet->load(['missions' => function ($query) {
            $query->orderBy('day_number');
        }, 'activeUsers']);
        
        return view('consultant.mission_sets.show', compact('missionSet'));
    }

    /**
     * Add a daily mission to the set.
     */
    public function storeMission(Request $request, string $locale, $missionSetId)
    {
        // Manual resolution to avoid route binding type hint errors
        $missionSet = $missionSetId instanceof MissionSet ? $missionSetId : MissionSet::findOrFail($missionSetId);

        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|max:30',
            'title.en' => 'required|string',
            'description.en' => 'nullable|string',
            'points' => 'required|integer|min:0',
            'is_body' => 'boolean',
            'is_mind' => 'boolean',
            'is_wisdom' => 'boolean',
        ]);

        // Check if day already exists
        if ($missionSet->missions()->where('day_number', $validated['day_number'])->exists()) {
            return back()->withErrors(['day_number' => 'A mission for this day already exists.']);
        }

        DailyMission::create([
            'mission_set_id' => $missionSet->id,
            'day_number' => $validated['day_number'],
            'title' => ['en' => $validated['title']['en']],
            'description' => ['en' => $validated['description']['en'] ?? ''],
            'points' => $validated['points'],
            'is_body' => $validated['is_body'] ?? false,
            'is_mind' => $validated['is_mind'] ?? false,
            'is_wisdom' => $validated['is_wisdom'] ?? false,
            'created_by_id' => auth()->id(),
        ]);

        return back()->with('success', 'Mission added successfully.');
    }

    /**
     * Assign the mission set to a user.
     */
    public function assign(Request $request, string $locale, $missionSetId)
    {
        try {
            // Manual resolution
            $missionSet = $missionSetId instanceof MissionSet ? $missionSetId : MissionSet::findOrFail($missionSetId);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Check if user already has an active mission set
            if ($user->active_mission_set_id && $user->active_mission_set_id !== $missionSet->id) {
                return back()->withErrors(['user_id' => 'User already has an active mission set. Please complete or reassign the current one first.']);
            }
            
            $user->update([
                'active_mission_set_id' => $missionSet->id,
                'mission_started_at' => $validated['start_date'],
            ]);

            return back()->with('success', "Mission Set assigned to {$user->name} starting from {$validated['start_date']}.");
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Mission assignment error: ' . $e->getMessage());
            return back()->withErrors(['general' => 'An error occurred while assigning the mission set: ' . $e->getMessage()]);
        }
    }
}
