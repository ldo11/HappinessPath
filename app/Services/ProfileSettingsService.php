<?php

namespace App\Services;

use App\Models\User;

class ProfileSettingsService
{
    public function update(User $user, array $data): User
    {
        $user->fill([
            'geo_privacy' => $data['geo_privacy'],
            'spiritual_preference' => $data['spiritual_preference'],
        ]);

        $user->save();

        return $user;
    }
}
