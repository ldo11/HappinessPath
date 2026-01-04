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
        try {
            // Debug: Log the request
            \Log::info('Consultant accessing user progress', [
                'consultant_id' => auth()->id(),
                'target_user_id' => $userId,
                'locale' => $request->route('locale')
            ]);

            $user = User::findOrFail($userId);

            // Ensure user belongs to consultant logic if needed, 
            // but typically consultant can view any user's progress or search for them.
            
            $user->load(['activeMissionSet.missions']);
            
            // Calculate current progress
            $currentMission = null;
            $currentDay = null;
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
            
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            \Log::error('Authentication error in user progress', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in user progress view', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'consultant_id' => auth()->id()
            ]);
            return back()->with('error', 'Unable to load user progress: ' . $e->getMessage());
        }
    }

    public function assign(Request $request, $userId)
    {
        try {
            // Debug: Log the request
            \Log::info('Consultant assigning mission set', [
                'consultant_id' => auth()->id(),
                'target_user_id' => $userId,
                'mission_set_id' => $request->input('mission_set_id'),
                'locale' => $request->route('locale'),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

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
            } elseif (!$user->mission_started_at) {
                // If user doesn't have a start date, set it now
                $updateData['mission_started_at'] = now();
            }

            $user->update($updateData);

            \Log::info('Mission set assigned successfully', [
                'user_id' => $user->id,
                'mission_set_id' => $data['mission_set_id'],
                'consultant_id' => auth()->id()
            ]);

            return back()->with('success', 'Mission set assigned successfully!');
            
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            \Log::error('Authentication error in mission assignment', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in mission assignment', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in mission assignment', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'consultant_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Unable to assign mission set: ' . $e->getMessage());
        }
    }
}
