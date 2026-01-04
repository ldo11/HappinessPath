<?php

namespace Tests\Feature\Consultant;

use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionSetAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function test_consultant_can_assign_mission_set_to_user()
    {
        //$this->withoutExceptionHandling();

        // 1. Create Consultant and User
        $consultant = User::factory()->create(['role' => 'consultant']);
        $user = User::factory()->create(['role' => 'user']);

        // 2. Create Mission Set
        $missionSet = MissionSet::create([
            'name' => ['en' => 'Healing Journey'],
            'type' => 'healing',
            'created_by' => $consultant->id,
        ]);

        // Ensure Mission Set exists
        $this->assertDatabaseHas('mission_sets', ['id' => $missionSet->id]);

        // 3. Assign
        // Debug: Ensure User starts with null
        $this->assertNull($user->active_mission_set_id);
        
        $response = $this->actingAs($consultant)
            ->post(route('user.admin.mission-sets.assign', ['locale' => 'en', 'missionSet' => $missionSet->id]), [
                'user_id' => $user->id,
                'start_date' => now()->toDateString(),
            ]);

        if (session('errors')) {
            dump(session('errors')->all());
        }

        // 4. Assert
        $response->assertSessionHas('success');
        
        $user->refresh();
        $this->assertEquals($missionSet->id, $user->active_mission_set_id);
        $this->assertEquals(now()->toDateString(), $user->mission_started_at->toDateString());
    }

    public function test_consultant_can_create_mission_set_and_add_missions()
    {
        $this->withoutExceptionHandling();

        $consultant = User::factory()->create(['role' => 'consultant']);

        // Create Set
        $response = $this->actingAs($consultant)
            ->post(route('user.admin.mission-sets.store', ['locale' => 'en']), [
                'name' => ['en' => 'New Program'],
                'description' => ['en' => 'Description'],
                'type' => 'growth',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mission_sets', ['type' => 'growth']);
        
        $missionSet = MissionSet::first();

        // Add Mission
        $response = $this->actingAs($consultant)
            ->post(route('user.admin.mission-sets.missions.store', ['locale' => 'en', 'missionSet' => $missionSet]), [
                'day_number' => 1,
                'title' => ['en' => 'Day 1'],
                'description' => ['en' => 'Desc'],
                'points' => 20,
                'is_mind' => 1,
            ]);

        if (!session()->has('success')) {
            dump(session('errors') ? session('errors')->all() : 'No errors in session');
        }

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('daily_missions', [
            'mission_set_id' => $missionSet->id,
            'day_number' => 1,
            'points' => 20
        ]);
    }
}
