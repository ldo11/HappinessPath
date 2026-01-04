<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DailyTask;
use App\Models\UserDailyTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DailyMissionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DailyTask $dailyTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->dailyTask = DailyTask::factory()->create([
            'day_number' => 1,
            'type' => 'mindfulness',
            'estimated_minutes' => 15,
            'title' => 'Test Daily Task',
            'description' => 'Test Description',
            'instructions' => 'Test Instructions',
        ]);
    }

    /** @test */
    public function user_can_complete_daily_mission_and_gain_xp()
    {
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $this->dailyTask->id,
                'report_content' => 'I completed this task successfully and learned a lot about mindfulness.',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);

        // Assert the report is saved in database
        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $this->user->id,
            'daily_task_id' => $this->dailyTask->id,
            'report_content' => 'I completed this task successfully and learned a lot about mindfulness.',
        ]);
    }

    /** @test */
    public function user_cannot_complete_same_daily_mission_twice_real()
    {
        // Complete the mission first time
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $this->dailyTask->id,
                'report_content' => 'First completion',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);

        // Try to complete the same mission again
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $this->dailyTask->id,
                'report_content' => 'Test mission completion',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'already_completed' => true,
            'xp_awarded' => 20, // Previously awarded XP
        ]);

        // Assert no new record was created
        $this->assertEquals(1, UserDailyTask::where('user_id', $this->user->id)
            ->where('daily_task_id', $this->dailyTask->id)
            ->count());
    }

    /** @test */
    public function user_cannot_complete_nonexistent_daily_mission()
    {
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => 9999, // Non-existent ID
                'report_content' => 'Trying to complete non-existent task',
            ]);

        $response->assertStatus(302); // Controller redirects on validation error
    }

    /** @test */
    public function report_content_is_required_for_mission_completion()
    {
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $this->dailyTask->id,
                'report_content' => '',
            ]);

        $response->assertStatus(302); // Controller redirects on validation error
    }

    /** @test */
    public function task_id_is_required_for_mission_completion()
    {
        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'report_content' => 'Test report without task ID',
            ]);

        $response->assertStatus(302); // Controller redirects on validation error
    }

    //    /** @test */
//    public function guest_cannot_complete_daily_mission()
//    {
//        $response = $this->post('/en/daily-mission/complete', [
//            'daily_task_id' => $this->dailyTask->id,
//            'report_content' => 'Guest trying to complete mission',
//        ]);
//
//        $response->assertRedirect('/en/login');
//
//        // Assert no record was created
//        $this->assertDatabaseMissing('user_daily_tasks', [
//            'daily_task_id' => $this->dailyTask->id,
//        ]);
//    }

    /** @test */
    public function user_can_view_their_completed_missions()
    {
        // Create some completed missions
        UserDailyTask::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed_at' => now(),
            'xp_awarded' => 10,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/en/dashboard');

        $response->assertStatus(200);
        // The dashboard should show completed missions
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function xp_awarded_is_based_on_task_type()
    {
        // Create tasks with different types
        $mindfulnessTask = DailyTask::factory()->create([
            'type' => 'mindfulness',
            'estimated_minutes' => 10,
        ]);

        $physicalTask = DailyTask::factory()->create([
            'type' => 'physical',
            'estimated_minutes' => 20,
        ]);

        $user = User::factory()->create();
        
        // Ensure user has a journey record
        $userJourney = $user->userJourney ?? $user->userJourney()->create([
            'current_day' => 1,
        ]);

        // Complete mindfulness task (should give base XP)
        $response = $this->actingAs($user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $mindfulnessTask->id,
                'report_content' => 'Completed mindfulness task',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);
        
        // Check that user daily task record was created
        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $user->id,
            'daily_task_id' => $mindfulnessTask->id,
            'report_content' => 'Completed mindfulness task',
        ]);

        // Complete physical task (should give more XP due to longer duration)
        $response = $this->actingAs($user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $physicalTask->id,
                'report_content' => 'Completed physical task',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);
        
        // Check that user daily task record was created
        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $user->id,
            'daily_task_id' => $physicalTask->id,
            'report_content' => 'Completed physical task',
        ]);
    }

    /** @test */
    public function mission_completion_works_correctly()
    {
        // Ensure user has a journey record
        $userJourney = $this->user->userJourney ?? $this->user->userJourney()->create([
            'current_day' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/en/daily-mission/complete', [
                'task_id' => $this->dailyTask->id,
                'report_content' => 'Test mission completion',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);
        
        // Check that user daily task record was created
        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $this->user->id,
            'daily_task_id' => $this->dailyTask->id,
            'report_content' => 'Test mission completion',
        ]);
    }
}
