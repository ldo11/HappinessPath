<?php

namespace App\Observers;

use App\Models\MissionSet;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // If user is not a regular member (e.g. admin/consultant), we might skip automatic assignment
        // or just assign it anyway so they can test. Let's assign if they don't have one.
        
        if (!$user->active_mission_set_id) {
            $defaultSet = MissionSet::where('is_default', true)->first() 
                ?? MissionSet::orderBy('id')->first();

            if ($defaultSet) {
                $user->update([
                    'active_mission_set_id' => $defaultSet->id,
                    'mission_started_at' => now(),
                ]);
            }
        }
    }
}
