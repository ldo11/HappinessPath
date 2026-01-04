<?php

namespace App\Services;

use App\Models\User;

class ProfileSettingsService
{
    public function update(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'nickname' => $data['nickname'] ?? null,
            'introduction' => $data['introduction'] ?? null,
            'location' => $data['location'] ?? null,
            'display_language' => $data['display_language'] ?? 'en',
            'city' => $data['city'] ?? null,
            'is_available' => $data['is_available'] ?? $user->is_available,
        ]);

        $user->save();

        if ($user->hasRole('consultant') && array_key_exists('consultant_pain_points', $data)) {
            $user->consultantPainPoints()->sync($data['consultant_pain_points'] ?? []);
        }

        return $user;
    }
}
