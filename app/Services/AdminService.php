<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQuizResult;
use App\Models\UserTree;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $payload = Arr::only($data, [
                'name',
                'email',
                'password',
                'role',
                'city',
                'spiritual_preference',
                'geo_privacy',
            ]);

            $payload['password'] = Hash::make((string) ($payload['password'] ?? ''));

            $user = User::create($payload);

            event(new Registered($user));

            return $user;
        });
    }

    public function resetAssessment(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $user = User::query()->findOrFail($userId);

            $user->onboarding_status = 'new';
            $user->save();

            UserQuizResult::withTrashed()->where('user_id', $userId)->forceDelete();

            $tree = UserTree::query()->firstOrNew(['user_id' => $userId]);
            $tree->season = 'spring';
            $tree->health = 0;
            $tree->exp = 0;
            $tree->fruits_balance = 0;
            $tree->total_fruits_given = 0;
            $tree->save();
        });
    }
}
