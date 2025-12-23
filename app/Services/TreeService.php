<?php

namespace App\Services;

use App\Models\UserTree;
use App\Models\UserJourney;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreeService
{
    /**
     * Process a completed meditation session and update user's tree
     */
    public function processMeditationSession(User $user, int $durationMinutes, string $meditationType = 'mindfulness'): array
    {
        try {
            DB::beginTransaction();

            $userTree = $user->userTree;
            $userJourney = $user->userJourney;

            // Calculate EXP based on duration and type
            $baseExp = $this->calculateMeditationExp($durationMinutes, $meditationType);
            
            // Calculate health improvement
            $healthImprovement = $this->calculateHealthImprovement($durationMinutes, $userTree->health);

            // Update user tree
            $userTree->exp += $baseExp;
            $userTree->health = min(100, $userTree->health + $healthImprovement);
            
            // Check for level up
            $leveledUp = false;
            $previousLevel = $userTree->season;
            
            if ($userTree->exp >= ($userTree->season * 100)) {
                $userTree->season += 1;
                $userTree->health = min(100, $userTree->health + 10); // Bonus health on level up
                $leveledUp = true;
            }

            // Award fruits for milestones
            $fruitsEarned = $this->calculateFruitReward($userTree->exp, $userTree->total_fruits_given);
            $userTree->fruits_balance += $fruitsEarned;

            $userTree->save();

            // Update user journey activity
            $userJourney->last_activity_at = now();
            $userJourney->save();

            DB::commit();

            return [
                'success' => true,
                'exp_gained' => $baseExp,
                'health_improved' => $healthImprovement,
                'new_health' => $userTree->health,
                'new_level' => $userTree->season,
                'leveled_up' => $leveledUp,
                'previous_level' => $previousLevel,
                'fruits_earned' => $fruitsEarned,
                'total_fruits' => $userTree->fruits_balance,
                'message' => $this->generateSessionMessage($baseExp, $healthImprovement, $leveledUp, $fruitsEarned)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TreeService meditation session failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process meditation session'
            ];
        }
    }

    /**
     * Calculate EXP gained from meditation
     */
    private function calculateMeditationExp(int $durationMinutes, string $type): int
    {
        $baseExp = max(5, intval($durationMinutes / 2)); // 1 EXP per 2 minutes, minimum 5
        
        // Type multipliers
        $multipliers = [
            'mindfulness' => 1.0,
            'breathing' => 1.2,
            'loving-kindness' => 1.5,
            'body-scan' => 1.3,
            'walking' => 1.1,
        ];

        $multiplier = $multipliers[$type] ?? 1.0;
        
        return intval($baseExp * $multiplier);
    }

    /**
     * Calculate health improvement from meditation
     */
    private function calculateHealthImprovement(int $durationMinutes, int $currentHealth): int
    {
        // Diminishing returns based on current health
        if ($currentHealth >= 90) {
            return 1; // Minimal improvement when already healthy
        } elseif ($currentHealth >= 70) {
            return min(3, intval($durationMinutes / 10));
        } elseif ($currentHealth >= 50) {
            return min(5, intval($durationMinutes / 8));
        } else {
            return min(8, intval($durationMinutes / 5)); // More improvement when withered
        }
    }

    /**
     * Calculate fruit rewards based on EXP milestones
     */
    private function calculateFruitReward(int $totalExp, int $totalFruitsGiven): int
    {
        // Award fruits every 50 EXP, but fewer if user already gives many fruits
        $expMilestone = intval($totalExp / 50);
        $fruitsFromMilestones = $expMilestone - $totalFruitsGiven;
        
        return max(0, $fruitsFromMilestones);
    }

    /**
     * Generate appropriate message for the session
     */
    private function generateSessionMessage(int $exp, int $health, bool $leveledUp, int $fruits): string
    {
        $message = "Great job! You earned {$exp} EXP and your tree's health improved by {$health} points.";
        
        if ($leveledUp) {
            $message .= " 🎉 Level up! Your tree is now stronger.";
        }
        
        if ($fruits > 0) {
            $message .= " You earned {$fruits} fruit" . ($fruits > 1 ? 's' : '') . " to share with others!";
        }
        
        return $message;
    }

    /**
     * Get tree status and recommendations
     */
    public function getTreeStatus(User $user): array
    {
        $userTree = $user->userTree;
        $health = $userTree->health;

        $status = [
            'health' => $health,
            'level' => $userTree->season,
            'exp' => $userTree->exp,
            'fruits' => $userTree->fruits_balance,
            'next_level_exp' => ($userTree->season * 100) - $userTree->exp,
        ];

        if ($health >= 80) {
            $status['category'] = 'thriving';
            $status['message'] = 'Your tree is thriving! Keep up the excellent work.';
            $status['recommendation'] = 'Try advanced meditation sessions to maintain your growth.';
            $status['color'] = 'green';
        } elseif ($health >= 50) {
            $status['category'] = 'growing';
            $status['message'] = 'Your tree is growing steadily.';
            $status['recommendation'] = 'Daily meditation will help your tree flourish.';
            $status['color'] = 'blue';
        } else {
            $status['category'] = 'withered';
            $status['message'] = 'Your tree needs care and attention.';
            $status['recommendation'] = 'Start with short, daily meditation sessions to nurture your tree back to health.';
            $status['color'] = 'yellow';
        }

        return $status;
    }

    /**
     * Get daily streak information
     */
    public function getStreakInfo(User $user): array
    {
        $userJourney = $user->userJourney;
        $lastActivity = $userJourney->last_activity_at;
        
        if (!$lastActivity) {
            return [
                'current_streak' => 0,
                'longest_streak' => 0,
                'days_missed' => 0,
                'is_active_today' => false
            ];
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $isActiveToday = $lastActivity->greaterThanOrEqualTo($today);
        $wasActiveYesterday = $lastActivity->greaterThanOrEqualTo($yesterday) && $lastActivity->lessThan($today);
        
        // Calculate current streak (simplified - in production, you'd track this properly)
        $currentStreak = $isActiveToday ? 1 : ($wasActiveYesterday ? 1 : 0);
        
        return [
            'current_streak' => $currentStreak,
            'longest_streak' => max($currentStreak, 1), // Placeholder
            'days_missed' => $isActiveToday ? 0 : 1,
            'is_active_today' => $isActiveToday
        ];
    }
}
