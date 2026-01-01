<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use App\Models\User;
use App\Models\UserJourney;
use App\Models\Donation;
use App\Models\UserDailyTask;
use App\Models\DailyMission;
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
        $user = Auth::user();

        // Get user data
        $userJourney = $user->userJourney ?? $this->createDefaultJourney($user);

        // Get today's task using JourneyService
        $todayTask = $this->journeyService->getTodayTask($user);

        $dailyMissionCompleted = false;
        if (isset($todayTask->id) && $todayTask->id) {
            // Only check completion for real database tasks (numeric IDs)
            if (is_numeric($todayTask->id)) {
                $dailyMissionCompleted = UserDailyTask::query()
                    ->where('user_id', $user->id)
                    ->where('daily_task_id', (int) $todayTask->id)
                    ->whereNotNull('completed_at')
                    ->exists();
            }
        }

        // Get tree status using JourneyService
        $treeStatus = $this->journeyService->getTreeStatus($user);

        $painPoints = collect();
        $userPainPoints = [];
        $topPainPoints = collect();
        if (Schema::hasTable('pain_points') && Schema::hasTable('user_pain_points')) {
            $painPoints = PainPoint::query()->orderBy('name')->get();
            $userPainPoints = $user->painPoints()->get()->keyBy('id')->map(function ($painPoint) {
                return (int) $painPoint->pivot->severity;
            })->all();

            $topPainPoints = $painPoints
                ->filter(function ($painPoint) use ($userPainPoints) {
                    return array_key_exists($painPoint->id, $userPainPoints) && ((int) $userPainPoints[$painPoint->id]) > 0;
                })
                ->sortByDesc(function ($painPoint) use ($userPainPoints) {
                    return (int) ($userPainPoints[$painPoint->id] ?? 0);
                })
                ->take(3);
        }
        $hasQuizResult = (bool) $user->quizResult;

        $dailyMissions = collect();
        if (Schema::hasTable('daily_missions')) {
            $dailyMissions = DailyMission::query()->latest('id')->limit(10)->get();
        }

        return view('dashboard', compact(
            'user',
            'userJourney',
            'todayTask',
            'dailyMissionCompleted',
            'treeStatus',
            'painPoints',
            'userPainPoints',
            'topPainPoints',
            'hasQuizResult',
            'dailyMissions'
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
        
        // Use JourneyService to complete task
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

    public function donateFruit(Request $request)
    {
        // UserTree functionality has been removed
        return response()->json([
            'success' => false,
            'message' => 'Fruit donation feature is currently unavailable.'
        ], 400);
    }
}
