<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQuizResult;
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

            // UserTree model has been removed - no tree reset needed
            // The reset functionality now focuses only on quiz results and onboarding status
        });
    }
}
