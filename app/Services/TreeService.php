<?php

namespace App\Services;

use App\Models\User;

class TreeService
{
    public function getTreeStatus(User $user): array
    {
        return [
            'health' => 50,
            'season' => 'spring',
            'level' => 'Cây non',
            'message' => 'Hành trình của bạn vừa bắt đầu. Hãy kiên trì thực hành mỗi ngày.',
            'icon' => 'fa-seedling',
        ];
    }

    public function getStreakInfo(User $user): array
    {
        return [
            'current_streak' => 0,
            'best_streak' => 0,
            'last_meditation_at' => null,
        ];
    }

    public function processMeditationSession(User $user, int $minutes, string $type): array
    {
        $minutes = max(1, min(60, $minutes));

        return [
            'success' => true,
            'message' => 'Meditation session completed',
            'minutes' => $minutes,
            'type' => $type,
            'xp_awarded' => 0,
            'tree_status' => $this->getTreeStatus($user),
            'streak_info' => $this->getStreakInfo($user),
        ];
    }
}
