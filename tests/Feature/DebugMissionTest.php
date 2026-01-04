<?php

namespace Tests\Feature;

use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugMissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_completion_logic()
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

        // Start 2 days ago => Day 3
        $user->update([
            'active_mission_set_id' => $missionSet->id,
            'mission_started_at' => now()->subDays(2), 
        ]);

        $user->refresh();
        dump('User Active Set ID: ' . $user->active_mission_set_id);
        dump('User Started At: ' . $user->mission_started_at);

        $response = $this->actingAs($user)->get('/en/dashboard');
        
        $data = $response->original->getData();
        $todayTask = $data['todayTask'];
        
        dump('Today Task Title: ' . ($todayTask->title ?? 'None'));
        dump('Is Completed Program: ' . ($todayTask->is_completed_program ?? 'False'));
        
        $response->assertSee('Congratulations!');
    }
}
