<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use App\Models\User;
use App\Models\UserJourney;
use App\Models\Donation;
use App\Models\UserDailyTask;
use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Services\JourneyService;
use App\Services\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    protected $journeyService;

    public function __construct(JourneyService $journeyService)
    {
        $this->journeyService = $journeyService;
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Eager load painPoints with pivot score
        $user->load(['painPoints', 'userJourney', 'activeMissionSet']);

        // Check for Active Mission Set (Priority)
        $todayTask = null;
        $missionSet = $user->activeMissionSet;
        $hasMissionSet = false;
        
        if ($missionSet) {
            $hasMissionSet = true;
            // Calculate current day based on completed missions count + 1
            $completedMissionsCount = $user->completedMissions()
                ->whereHas('missionSet', function ($query) use ($missionSet) {
                    $query->where('id', $missionSet->id);
                })
                ->count();
            
            $currentDay = $completedMissionsCount + 1;
            
            // Find mission for this day
            $mission = DailyMission::where('mission_set_id', $missionSet->id)
                ->where('day_number', $currentDay)
                ->first();

            if ($mission) {
                // Adapt DailyMission to view-compatible structure
                $todayTask = (object) [
                    'id' => $mission->id,
                    'title' => $mission->getTranslation('title', app()->getLocale()), 
                    'description' => $mission->getTranslation('description', app()->getLocale()),
                    'type' => $mission->is_body ? 'physical' : ($mission->is_wisdom ? 'wisdom' : 'mindfulness'),
                    'difficulty' => 'medium', // Default
                    'estimated_minutes' => $mission->estimated_minutes ?? 15, // Default
                    'instructions' => [], // DailyMission doesn't have instructions column yet
                    'solution_id' => null,
                    'points' => $mission->points ?? 10, // Add points from mission
                ];
            } else {
                // Check if program is finished (Day > Max Day)
                $maxDay = DailyMission::where('mission_set_id', $missionSet->id)->max('day_number');
                
                if ($currentDay > $maxDay) {
                     $todayTask = (object) [
                        'id' => null, // No task
                        'title' => __('dashboard.program_completed_title'),
                        'description' => __('dashboard.program_completed_desc'),
                        'is_completed_program' => true,
                        'type' => 'wisdom',
                        'difficulty' => 'easy',
                        'estimated_minutes' => 0,
                        'instructions' => [],
                        'solution_id' => null,
                     ];
                }
            }
        }

        // Fallback to Standard 90-Day Journey ONLY if no Mission Set task found AND not finished
        // If todayTask is set (either mission or completed object), skip fallback
        $userJourney = $user->userJourney ?? $this->createDefaultJourney($user);
        
        if (! $todayTask) {
            $todayTask = $this->journeyService->getTodayTask($user);
        } else {
            // Override current day for display if using Mission Set
            if (isset($todayTask->is_completed_program) && $todayTask->is_completed_program) {
                 $userJourney->current_day = $maxDay ?? 30; // Show last day or max
            } else {
                 $userJourney->current_day = $currentDay ?? 1;
            }
        }

        $dailyMissionCompleted = false;
        if (isset($todayTask->id) && $todayTask->id) {
            // Only check completion for real database tasks (numeric IDs)
            if (is_numeric($todayTask->id)) {
                $dailyMissionCompleted = $user->completedMissions()
                    ->where('daily_mission_id', (int) $todayTask->id)
                    ->exists();
            }
        }

        // Get tree status using JourneyService
        $treeStatus = $this->journeyService->getTreeStatus($user);

        // Pain Points Logic
        // We pass the user's active pain points to the view.
        // We also pass all available pain points if needed for the update modal/page, 
        // but the view 'dashboard' might only need the user's current ones.
        // The request asks for "My Pain Points" list.
        $myPainPoints = $user->painPoints->sortByDesc(function ($pp) {
            return (int) $pp->pivot->score;
        });

        $hasQuizResult = (bool) $user->quizResult;

        $dailyMissions = collect();
        if (Schema::hasTable('daily_missions')) {
            $dailyMissions = DailyMission::query()->latest('id')->limit(10)->get();
        }

        // Calculate Levels (Simple Logic: Level = Floor(XP / 100) + 1)
        $levels = [
            'body' => intval($user->xp_body / 100) + 1,
            'mind' => intval($user->xp_mind / 100) + 1,
            'wisdom' => intval($user->xp_wisdom / 100) + 1,
        ];
        
        // Calculate Progress % within current level (XP % 100)
        $progress = [
            'body' => $user->xp_body % 100,
            'mind' => $user->xp_mind % 100,
            'wisdom' => $user->xp_wisdom % 100,
        ];

        // Debugging for Test - Exception to prove execution
        // throw new \Exception("DEBUG: Controller Index Reached. TodayTask: " . json_encode($todayTask));

        return view('dashboard', compact(
            'user',
            'userJourney',
            'todayTask',
            'dailyMissionCompleted',
            'treeStatus',
            'myPainPoints',
            'hasQuizResult',
            'dailyMissions',
            'levels',
            'progress',
            'missionSet',
            'hasMissionSet'
        ));
    }

    private function createDefaultJourney(User $user)
    {
        return UserJourney::firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_day' => 1,
                'last_activity_at' => now(),
            ]
        );
    }

    public function completeTask(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has an active mission set
        $missionSet = $user->activeMissionSet;
        
        if ($missionSet) {
            // Handle mission set completion
            $missionId = $request->input('mission_id');
            
            if (!$missionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No mission specified.'
                ]);
            }
            
            $mission = DailyMission::where('mission_set_id', $missionSet->id)
                ->where('id', $missionId)
                ->first();
                
            if (!$mission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mission not found.'
                ]);
            }
            
            // Check if already completed
            $alreadyCompleted = $user->completedMissions()
                ->where('daily_mission_id', $mission->id)
                ->exists();
                
            if ($alreadyCompleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mission already completed.'
                ]);
            }
            
            // Complete the mission
            $user->completedMissions()->attach($mission->id, [
                'completed_at' => now(),
                'xp_earned' => $mission->points ?? 10,
            ]);
            
            // Award XP based on mission type
            if ($mission->is_body) {
                $user->increment('xp_body', $mission->points ?? 10);
            } elseif ($mission->is_mind) {
                $user->increment('xp_mind', $mission->points ?? 10);
            } elseif ($mission->is_wisdom) {
                $user->increment('xp_wisdom', $mission->points ?? 10);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Mission completed! You earned ' . ($mission->points ?? 10) . ' XP!'
            ]);
        } else {
            // Use JourneyService for standard 90-day journey
            $success = $this->journeyService->completeTodayTask($user);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tuyệt vời! Bạn đã hoàn thành nhiệm vụ hôm nay và nhận được 10 EXP!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã hoàn thành tất cả 30 ngày của hành trình!'
                ]);
            }
        }
    }

    public function donateFruit(Request $request)
    {
        // UserTree functionality has been removed
        return response()->json([
            'success' => false,
            'message' => 'Fruit donation feature is currently unavailable.'
        ], 400);
    }
}
