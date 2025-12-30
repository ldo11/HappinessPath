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
        $userTree = $user->userTree;
        
        if (!$userTree) {
            // Create tree if it doesn't exist
            $userTree = \App\Models\UserTree::create([
                'user_id' => $user->id,
                'season' => 'spring',
                'health' => 50,
                'exp' => 0,
                'fruits_balance' => 0,
                'total_fruits_given' => 0,
            ]);
        }

        $userTree->exp += $points;
        
        // Update health based on experience
        $userTree->health = min(100, $userTree->health + 5);
        
        // Update season based on experience
        if ($userTree->exp >= 1000) {
            $userTree->season = 'winter';
        } elseif ($userTree->exp >= 500) {
            $userTree->season = 'autumn';
        } elseif ($userTree->exp >= 200) {
            $userTree->season = 'summer';
        }
        
        $userTree->save();
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
        $userTree = $user->userTree;
        
        if (!$userTree) {
            return [
                'health' => 50,
                'season' => 'spring',
                'exp' => 0,
                'message' => 'Cây của bạn đang bắt đầu phát triển',
                'icon' => 'fa-seedling',
                'color' => 'text-green-500',
                'level' => 'Mầm cây',
                'next_level' => 10
            ];
        }

        $health = $userTree->health;
        $season = $userTree->season;
        $exp = $userTree->exp;

        // Determine tree status based on health
        if ($health < 30) {
            $status = [
                'message' => 'Cây của bạn cần sự chăm sóc',
                'icon' => 'fa-tree',
                'color' => 'text-gray-500',
                'level' => 'Cây khô',
                'next_level' => 30
            ];
        } elseif ($health < 50) {
            $status = [
                'message' => 'Cây của bạn đang phát triển',
                'icon' => 'fa-tree',
                'color' => 'text-yellow-500',
                'level' => 'Cây non',
                'next_level' => 50
            ];
        } elseif ($health < 80) {
            $status = [
                'message' => 'Cây của bạn đang khỏe mạnh',
                'icon' => 'fa-tree',
                'color' => 'text-emerald-500',
                'level' => 'Cây trưởng thành',
                'next_level' => 80
            ];
        } else {
            $status = [
                'message' => 'Cây của bạn tràn đầy sức sống!',
                'icon' => 'fa-tree',
                'color' => 'text-green-600',
                'level' => 'Cây cổ thụ',
                'next_level' => 100
            ];
        }

        $age = null;
        if ($user->dob) {
            try {
                $age = $user->dob->age;
            } catch (\Throwable $e) {
                $age = null;
            }
        }

        if ($age !== null && $age < 18) {
            $status['icon'] = 'fa-seedling';
        }

        return array_merge([
            'health' => $health,
            'season' => $season,
            'exp' => $exp
        ], $status);
    }
}
