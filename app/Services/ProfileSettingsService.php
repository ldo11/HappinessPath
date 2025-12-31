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
            'language' => $data['language'] ?? ($user->language ?? 'vi'),
            'religion' => $data['religion'] ?? null,
            'is_available' => $data['is_available'] ?? $user->is_available,
        ]);

        $user->save();

        if ($user->hasRole('consultant') && array_key_exists('consultant_pain_points', $data)) {
            $user->consultantPainPoints()->sync($data['consultant_pain_points'] ?? []);
        }

        return $user;
    }
}
