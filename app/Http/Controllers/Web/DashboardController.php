<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTree;
use App\Models\UserJourney;
use App\Models\DailyTask;
use App\Models\Donation;
use App\Services\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Redirect to onboarding if not completed
        if (!$user->onboarding_completed) {
            return redirect()->route('onboarding.step1');
        }

        $userTree = $user->userTree;
        $userJourney = $user->userJourney;

        // Get today's daily task
        $todayTask = $this->getTodayTask($user, $userJourney);

        // Get nearby users for fruit donation
        $communityService = new CommunityService();
        $nearbyUsers = $communityService->findNearbyBuddies($user);

        // Get recent donations
        $recentDonations = Donation::with('receiver')
            ->where('giver_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        // Calculate tree status
        $treeStatus = $this->getTreeStatus($userTree);

        return view('dashboard', compact(
            'user',
            'userTree', 
            'userJourney',
            'todayTask',
            'nearbyUsers',
            'recentDonations',
            'treeStatus'
        ));
    }

    private function getTodayTask(User $user, UserJourney $journey)
    {
        // Get task for current day
        $task = DailyTask::where('day_number', $journey->current_day)
            ->where('difficulty', $this->getTaskDifficulty($user))
            ->first();

        if (!$task) {
            // Create a default task if none exists
            $task = DailyTask::create([
                'day_number' => $journey->current_day,
                'title' => 'Take 5 deep breaths',
                'description' => 'Find a quiet moment and take 5 deep, mindful breaths. Focus on the sensation of air entering and leaving your body.',
                'type' => 'mindfulness',
                'difficulty' => 'easy',
                'estimated_minutes' => 2,
            ]);
        }

        return $task;
    }

    private function getTaskDifficulty(User $user)
    {
        // Base difficulty on user's tree health and quiz results
        $treeHealth = $user->userTree->health ?? 50;
        
        if ($treeHealth >= 80) {
            return 'hard';
        } elseif ($treeHealth >= 50) {
            return 'medium';
        } else {
            return 'easy';
        }
    }

    private function getTreeStatus(UserTree $tree)
    {
        $health = $tree->health ?? 20;
        
        if ($health >= 80) {
            return [
                'status' => 'thriving',
                'icon' => 'fa-tree',
                'color' => 'text-green-600',
                'bg_color' => 'bg-green-100',
                'message' => 'Your tree is thriving! 🌳',
                'next_level' => 100 - $health,
                'level' => 'Flourishing'
            ];
        } elseif ($health >= 50) {
            return [
                'status' => 'growing',
                'icon' => 'fa-seedling',
                'color' => 'text-green-500',
                'bg_color' => 'bg-green-50',
                'message' => 'Your tree is growing! 🌱',
                'next_level' => 80 - $health,
                'level' => 'Growing'
            ];
        } else {
            return [
                'status' => 'withered',
                'icon' => 'fa-tree',
                'color' => 'text-yellow-600',
                'bg_color' => 'bg-yellow-50',
                'message' => 'Your tree needs care 🍂',
                'next_level' => 50 - $health,
                'level' => 'Withered'
            ];
        }
    }

    public function completeTask(Request $request)
    {
        $user = Auth::user();
        $userJourney = $user->userJourney;
        $userTree = $user->userTree;

        // Award EXP for completing task
        $expGained = 10;
        $userTree->exp += $expGained;
        
        // Improve tree health slightly
        $userTree->health = min(100, $userTree->health + 2);
        
        // Check for level up
        if ($userTree->exp >= ($userTree->season * 100)) {
            $userTree->season += 1;
            $userTree->health = min(100, $userTree->health + 5);
        }

        $userTree->save();

        // Move to next day
        $userJourney->current_day += 1;
        $userJourney->last_activity_at = now();
        $userJourney->save();

        return response()->json([
            'success' => true,
            'exp_gained' => $expGained,
            'new_health' => $userTree->health,
            'new_level' => $userTree->season,
            'message' => "Great job! You earned {$expGained} EXP and your tree is healthier!"
        ]);
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
