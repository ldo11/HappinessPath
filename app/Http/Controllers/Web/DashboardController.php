<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use App\Models\User;
use App\Models\UserTree;
use App\Models\UserJourney;
use App\Models\Donation;
use App\Models\UserDailyTask;
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
        $userTree = $user->userTree ?? $this->createDefaultTree($user);
        $userJourney = $user->userJourney ?? $this->createDefaultJourney($user);

        // Get today's task using JourneyService
        $todayTask = $this->journeyService->getTodayTask($user);

        $dailyMissionCompleted = false;
        if (isset($todayTask->id) && $todayTask->id) {
            $dailyMissionCompleted = UserDailyTask::query()
                ->where('user_id', $user->id)
                ->where('daily_task_id', (int) $todayTask->id)
                ->whereNotNull('completed_at')
                ->exists();
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

        return view('dashboard', compact(
            'user',
            'userTree', 
            'userJourney',
            'todayTask',
            'dailyMissionCompleted',
            'treeStatus',
            'painPoints',
            'userPainPoints',
            'topPainPoints',
            'hasQuizResult'
        ));
    }

    private function createDefaultTree(User $user)
    {
        return UserTree::firstOrCreate(
            ['user_id' => $user->id],
            [
                'season' => 'spring',
                'health' => 50,
                'exp' => 0,
                'fruits_balance' => 0,
                'total_fruits_given' => 0,
            ]
        );
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
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:' . Auth::id(),
            'message' => 'nullable|string|max:200',
        ]);

        $giver = Auth::user();
        $receiver = User::find($validated['receiver_id']);

        // Check if giver has fruits to donate
        if ($giver->userTree->fruits_balance < 1) {
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have any fruits to donate yet. Complete more tasks to earn fruits!'
            ], 400);
        }

        try {
            \DB::transaction(function () use ($giver, $receiver, $validated) {
                // Create donation record
                Donation::create([
                    'giver_id' => $giver->id,
                    'receiver_id' => $receiver->id,
                    'amount' => 1,
                    'message' => $validated['message'] ?? 'Sending you positive energy! 🌟',
                ]);

                // Update fruit balances
                $giver->userTree->fruits_balance -= 1;
                $giver->userTree->total_fruits_given += 1;
                $giver->userTree->save();

                $receiver->userTree->fruits_balance += 1;
                $receiver->userTree->save();

                // Award EXP to giver for generosity
                $giver->userTree->exp += 5;
                $giver->userTree->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Fruit donated successfully! You earned 5 EXP for your generosity.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
}
