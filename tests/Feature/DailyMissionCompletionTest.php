<?php

namespace Tests\Feature;

use App\Models\DailyTask;
use App\Models\User;
use App\Models\UserDailyTask;
use App\Models\UserTree;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyMissionCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_daily_mission_and_only_get_xp_once(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        UserTree::create([
            'user_id' => $user->id,
            'season' => 'spring',
            'health' => 50,
            'exp' => 0,
            'fruits_balance' => 0,
            'total_fruits_given' => 0,
        ]);

        $task = DailyTask::create([
            'day_number' => 1,
            // Current schema uses an int difficulty column.
            'difficulty_level_int' => 1,
            'solution_id' => null,
        ]);

        $first = $this->actingAs($user)->postJson('/en/daily-mission/complete', [
            'task_id' => $task->id,
            'report_content' => 'It was good',
        ]);

        $first->assertOk();
        $first->assertJson([
            'success' => true,
            'already_completed' => false,
            'xp_awarded' => 20,
        ]);

        $tree = $user->userTree()->firstOrFail();
        $this->assertSame(20, (int) $tree->exp);

        $second = $this->actingAs($user)->postJson('/en/daily-mission/complete', [
            'task_id' => $task->id,
            'report_content' => 'Trying again',
        ]);

        $second->assertOk();
        $second->assertJson([
            'success' => true,
            'already_completed' => true,
            'xp_awarded' => 20,
        ]);

        $tree->refresh();
        $this->assertSame(20, (int) $tree->exp);

        $this->assertDatabaseCount('user_daily_tasks', 1);

        $log = UserDailyTask::where('user_id', $user->id)->where('daily_task_id', $task->id)->firstOrFail();
        $this->assertNotNull($log->completed_at);
        $this->assertSame(20, (int) $log->xp_awarded);
        $this->assertSame('It was good', $log->report_content);
    }
}
