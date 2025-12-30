<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserJourney;
use App\Models\DailyTask;

class JourneyService
{
    /**
     * Get today's task for the user
     */
    public function getTodayTask(User $user)
    {
        $userJourney = $user->userJourney;
        
        if (!$userJourney) {
            // Create journey if it doesn't exist
            $userJourney = UserJourney::create([
                'user_id' => $user->id,
                'current_day' => 1,
                'last_activity_at' => now(),
            ]);
        }

        // Get task for current day
        $task = DailyTask::where('day_number', $userJourney->current_day)
            ->where('status', 'active')
            ->first();

        if (!$task) {
            // Fallback task if no specific task found
            return $this->createFallbackTask($userJourney->current_day);
        }

        return $task;
    }

    /**
     * Create a fallback task for when no specific task is found
     */
    private function createFallbackTask(int $day)
    {
        return (object) [
            'id' => null,
            'day_number' => $day,
            'title' => "Ngày {$day} - Thực hành chánh niệm",
            'description' => "Dành thời gian 5-10 phút để tập trung vào hơi thở và quan sát cảm xúc trong lòng. Đây là bước đầu tiên trên hành trình chữa lành.",
            'type' => 'mindfulness',
            'difficulty' => 'easy',
            'estimated_minutes' => 10,
            'solution_id' => null,
            'completed_at' => null,
            'instructions' => [
                'Tìm một không gian yên tĩnh',
                'Ngồi xuống với tư thế thoải mái',
                'Nhắm mắt và tập trung vào hơi thở',
                'Quan sát cảm xúc mà không phán xét'
            ]
        ];
    }

    /**
     * Complete today's task and move to next day
     */
    public function completeTodayTask(User $user)
    {
        $userJourney = $user->userJourney;
        
        if (!$userJourney) {
            return false;
        }

        // Move to next day (max 30 days)
        if ($userJourney->current_day < 30) {
            $userJourney->current_day += 1;
            $userJourney->last_activity_at = now();
            $userJourney->save();

            // Award experience points
            $this->awardExperience($user, 10);

            return true;
        }

        return false;
    }

    /**
     * Award experience points to user
     */
    private function awardExperience(User $user, int $points)
    {
        // UserTree model has been removed - experience awarding is disabled
        // This functionality could be replaced with a different progress tracking system
        return;
    }

    /**
     * Get journey progress percentage
     */
    public function getProgressPercentage(User $user)
    {
        $userJourney = $user->userJourney;
        
        if (!$userJourney) {
            return 0;
        }

        return ($userJourney->current_day / 30) * 100;
    }

    /**
     * Get tree status information
     */
    public function getTreeStatus(User $user)
    {
        // UserTree model has been removed - return default tree status
        return [
            'health' => 50,
            'season' => 'spring',
            'level' => 'Cây non',
            'message' => 'Hành trình của bạn vừa bắt đầu. Hãy kiên trì thực hành mỗi ngày.',
            'icon' => 'fa-seedling'
        ];
    }
}
