<?php

namespace Tests\Feature\Debug;

use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionDebugTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_mission_completion()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'language' => 'en',
            'display_language' => 'en',
        ]);

        $missionSet = MissionSet::create([
            'name' => ['en' => 'Test Journey'],
            'type' => 'growth',
            'created_by' => $user->id,
        ]);

        DailyMission::create([
            'mission_set_id' => $missionSet->id,
            'day_number' => 1,
            'title' => ['en' => 'Day 1 Task'],
            'points' => 10,
            'created_by_id' => $user->id,
        ]);

        // Start 2 days ago, so today is Day 3
        $user->update([
            'active_mission_set_id' => $missionSet->id,
            'mission_started_at' => now()->subDays(2), 
        ]);

        $response = $this->actingAs($user)->get('/en/dashboard');
        
        $data = $response->original->getData();
        $todayTask = $data['todayTask'];
        
        dump([
            'current_day' => $data['userJourney']->current_day,
            'todayTask_title' => $todayTask->title ?? 'N/A',
            'is_completed' => $todayTask->is_completed_program ?? false,
            'mission_set_id' => $user->active_mission_set_id,
            'mission_started_at' => $user->mission_started_at,
        ]);
    }
}
