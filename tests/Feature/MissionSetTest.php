<?php

namespace Tests\Feature;

use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MissionSetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic languages if needed (or rely on RefreshDatabase + manual setup)
        // We need language for dashboard route
    }

    public function test_user_sees_assigned_mission_set_on_dashboard()
    {
        // 1. Create User
        $user = User::factory()->create([
            'language' => 'en',
            'display_language' => 'en',
        ]);

        // 2. Create Mission Set
        $missionSet = MissionSet::create([
            'name' => ['en' => 'Test Journey'],
            'type' => 'growth',
            'created_by' => $user->id,
        ]);

        // 3. Create Daily Mission for Day 1
        DailyMission::create([
            'mission_set_id' => $missionSet->id,
            'day_number' => 1,
            'title' => ['en' => 'Day 1 Task'],
            'description' => ['en' => 'Do something today.'],
            'points' => 10,
            'created_by_id' => $user->id,
        ]);

        // 4. Assign to User (Day 1 is today)
        $user->update([
            'active_mission_set_id' => $missionSet->id,
            'mission_started_at' => now(),
        ]);

        // 5. Visit Dashboard
        $response = $this->actingAs($user)->get('/en/dashboard');

        // 6. Assert
        $response->assertStatus(200);
        $response->assertSee('Test Journey'); // Mission Set Name
        $response->assertSee('Day 1 Task'); // Daily Mission Title
    }

    public function test_user_sees_completed_state_when_program_finished()
    {
        $this->withoutExceptionHandling();
        
        // 1. Create User
        $user = User::factory()->create([
            'language' => 'en',
            'display_language' => 'en',
        ]);

        // 2. Create Mission Set
        $missionSet = MissionSet::create([
            'name' => ['en' => 'Test Journey'],
            'type' => 'growth',
            'created_by' => $user->id,
        ]);

        // 3. Create Daily Mission for Day 1 only
        DailyMission::create([
            'mission_set_id' => $missionSet->id,
            'day_number' => 1,
            'title' => ['en' => 'Day 1 Task'],
            'points' => 10,
            'created_by_id' => $user->id,
        ]);
        
        $this->assertDatabaseHas('daily_missions', ['mission_set_id' => $missionSet->id]);

        // 4. Assign to User (Start date was 2 days ago, so today would be Day 3)
        // Day 1: 2 days ago
        // Day 2: 1 day ago
        // Day 3: Today
        // Max day is 1. Current day is 3. 3 > 1.
        $user->update([
            'active_mission_set_id' => $missionSet->id,
            'mission_started_at' => now()->subDays(2), 
        ]);

        // 5. Visit Dashboard
        $response = $this->actingAs($user)->get('/en/dashboard');
        
        // Debug if failing
        $content = $response->getContent();
        if (!str_contains($content, 'Congratulations!')) {
             // dump($content); // Dump HTML to see what's rendered
             $data = $response->original->getData();
             dump('Today Task Title:', $data['todayTask']->title ?? 'N/A');
             dump('Is Completed:', $data['todayTask']->is_completed_program ?? 'false');
        }

        // 6. Assert
        $response->assertStatus(200);
        
        if (!str_contains($response->getContent(), 'Congratulations!')) {
            $data = $response->original->getData();
            dump("View Data TodayTask:", $data['todayTask']);
            // dump(substr($response->getContent(), 0, 2000)); // Dump start of HTML
        }

        // The view displays 'Congratulations!' (program_completed_title)
        $response->assertSee('Congratulations!'); 
    }

    public function test_fallback_to_default_journey_if_no_mission_set()
    {
        // 1. Create User
        $user = User::factory()->create([
            'language' => 'en',
            'display_language' => 'en',
            'active_mission_set_id' => null,
        ]);

        // 2. Visit Dashboard
        $response = $this->actingAs($user)->get('/en/dashboard');

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee("Today&#039;s Practice", false); // Default title escaped or raw
        // Or check for default content logic if no daily tasks seeded
    }
}
