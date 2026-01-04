<?php

namespace App\Policies;

use App\Models\DailyMission;
use App\Models\User;

class DailyMissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function view(User $user, DailyMission $dailyMission): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function update(User $user, DailyMission $dailyMission): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function delete(User $user, DailyMission $dailyMission): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }
}
