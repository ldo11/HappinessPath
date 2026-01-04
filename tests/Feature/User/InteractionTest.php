<?php

namespace Tests\Feature\User;

use App\Models\Assessment;
use App\Models\AssessmentOption;
use App\Models\AssessmentQuestion;
use App\Models\DailyMission;
use App\Models\User;
use App\Models\UserAssessment;
use App\Models\UserDailyTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_or_delete_consultant_content(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $this->actingAs($user)
            ->post('/en/consultant/assessments', [
                'title' => ['vi' => 'x'],
                'description' => ['vi' => 'x'],
                'questions' => [
                    [
                        'order' => 1,
                        'type' => 'single_choice',
                        'content' => ['vi' => 'Q'],
                        'options' => [
                            ['content' => ['vi' => 'A'], 'score' => 1],
                            ['content' => ['vi' => 'B'], 'score' => 2],
                        ],
                    ],
                ],
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete('/en/consultant/videos/1')
            ->assertForbidden();
    }

    public function test_user_can_submit_assessment_creates_user_assessment_record(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin']);

        $assessment = Assessment::query()->create([
            'title' => ['vi' => 'Bài test'],
            'description' => ['vi' => 'Mô tả'],
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $q = AssessmentQuestion::query()->create([
            'assessment_id' => $assessment->id,
            'content' => ['vi' => 'Câu hỏi 1'],
            'type' => 'single_choice',
            'order' => 1,
        ]);

        $optA = AssessmentOption::query()->create([
            'question_id' => $q->id,
            'content' => ['vi' => 'A'],
            'score' => 1,
        ]);

        $optB = AssessmentOption::query()->create([
            'question_id' => $q->id,
            'content' => ['vi' => 'B'],
            'score' => 5,
        ]);

        $res = $this->actingAs($user)->postJson("/en/assessments/{$assessment->id}", [
            'answers' => [
                $q->id => $optB->id,
            ],
            'submission_mode' => 'self_review',
        ]);

        $res->assertOk();

        $this->assertDatabaseHas('user_assessments', [
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'submission_mode' => 'self_review',
        ]);

        $ua = UserAssessment::query()->where('user_id', $user->id)->where('assessment_id', $assessment->id)->firstOrFail();
        $this->assertIsArray($ua->answers);
        $this->assertSame($optB->id, $ua->answers[$q->id]);
    }

    public function test_user_can_complete_mission_and_cannot_complete_twice(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        $consultant = User::factory()->create(['role' => 'consultant']);

        $mission = DailyMission::query()->create([
            'title' => ['en' => 'M1'],
            'description' => ['en' => 'D'],
            'points' => 10,
            'created_by_id' => $consultant->id,
        ]);

        // Complete uses task_id, and controller creates a placeholder daily_tasks row if missing.
        $first = $this->actingAs($user)->postJson('/en/daily-mission/complete', [
            'task_id' => $mission->id,
            'report_content' => 'done',
        ]);
        $first->assertOk();
        $first->assertJson([
            'success' => true,
            'already_completed' => false,
        ]);

        $this->assertDatabaseHas('user_daily_tasks', [
            'user_id' => $user->id,
            'daily_task_id' => $mission->id,
        ]);

        $second = $this->actingAs($user)->postJson('/en/daily-mission/complete', [
            'task_id' => $mission->id,
            'report_content' => 'done again',
        ]);
        $second->assertOk();
        $second->assertJson([
            'success' => false,
            'already_completed' => true,
        ]);

        $this->assertSame(
            1,
            UserDailyTask::query()->where('user_id', $user->id)->where('daily_task_id', $mission->id)->count()
        );
    }
}
