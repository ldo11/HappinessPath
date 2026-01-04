<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function view(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('admin') || $user->hasRole('consultant');
    }
}
