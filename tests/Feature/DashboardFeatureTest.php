<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PainPoint;
use App\Models\DailyTask;
use App\Models\UserJourney;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_soul_stats()
    {
        $user = User::factory()->create([
            'xp_body' => 120,
            'xp_mind' => 250,
            'xp_wisdom' => 50,
            'nickname' => 'ZenMaster',
            'introduction' => 'I am calm.'
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Body (Thân)')
            ->assertSee('120') // XP
            ->assertSee('Lv. 2') // 120 / 100 + 1
            ->assertSee('Mind (Tâm)')
            ->assertSee('250')
            ->assertSee('Lv. 3')
            ->assertSee('Wisdom (Trí)')
            ->assertSee('50')
            ->assertSee('Lv. 1')
            ->assertSee('ZenMaster')
            ->assertSee('I am calm.');
    }

    public function test_dashboard_displays_pain_points_list()
    {
        $user = User::factory()->create();
        $painPoint = PainPoint::create([
            'name' => 'Back Pain', 
            'status' => 'active',
            'category' => 'physical'
        ]);
        
        $user->painPoints()->attach($painPoint->id, ['score' => 8]);

        $this->actingAs($user)
            ->get(route('dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('My Pain Points')
            ->assertSee('Back Pain')
            ->assertSee('8/10');
    }

    public function test_dashboard_link_to_update_pain_points()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get(route('dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertSee(route('user.pain-points.index'));
    }

    public function test_complete_mission_increases_xp()
    {
        $user = User::factory()->create([
            'xp_mind' => 0,
        ]);

        // Create a daily task/mission
        $task = DailyTask::create([
            'day_number' => 1,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'type' => 'mindfulness',
            'difficulty' => 'easy',
            'estimated_minutes' => 10,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('daily-mission.complete', ['locale' => 'en']), [
                'task_id' => $task->id,
                'report_content' => 'I did it',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        // Verify XP increased
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'xp_mind' => 20, // 0 + 20
        ]);
        
        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $user->id,
            'daily_task_id' => $task->id,
            'xp_awarded' => 20,
        ]);
    }
}
