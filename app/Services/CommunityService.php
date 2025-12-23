<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CommunityService
{
    public function findNearbyBuddies(User $user): Collection
    {
        if (!$user->city) {
            return collect();
        }

        return User::query()
            ->whereKeyNot($user->getKey())
            ->where('city', $user->city)
            ->where('geo_privacy', false)
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get();
    }
}
