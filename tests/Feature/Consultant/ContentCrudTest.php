<?php

namespace Tests\Feature\Consultant;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\DailyMission;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_crud_assessment_and_questions_and_cannot_modify_others(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);

        $createPayload = [
            'title' => ['vi' => 'Bài đánh giá ngủ'],
            'description' => ['vi' => 'Mô tả tiếng Việt'],
            'questions' => [
                [
                    'order' => 1,
                    'type' => 'single_choice',
                    'content' => ['vi' => 'Bạn ngủ ngon không?'],
                    'options' => [
                        ['content' => ['vi' => 'Có'], 'score' => 5],
                        ['content' => ['vi' => 'Không'], 'score' => 1],
                    ],
                ],
            ],
        ];

        $res = $this->actingAs($consultant)->post('/en/consultant/assessments', $createPayload);
        $res->assertStatus(302);

        $assessment = Assessment::query()->where('created_by', $consultant->id)->latest('id')->firstOrFail();

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'created_by' => $consultant->id,
        ]);

        $this->assertSame('Bài đánh giá ngủ', $assessment->getTranslation('title', 'vi'));
        $this->assertSame('Mô tả tiếng Việt', $assessment->getTranslation('description', 'vi'));

        $addQuestionPayload = [
            'content' => ['vi' => 'Câu hỏi thêm'],
            'type' => 'single_choice',
            'order' => 2,
            'options' => [
                ['content' => ['vi' => 'Lựa chọn 1'], 'score' => 3],
                ['content' => ['vi' => 'Lựa chọn 2'], 'score' => 5],
            ],
        ];

        $res = $this->actingAs($consultant)->post("/en/consultant/assessments/{$assessment->id}/questions", $addQuestionPayload);
        $res->assertStatus(302);

        $this->assertDatabaseHas('assessment_questions', [
            'assessment_id' => $assessment->id,
            'order' => 2,
        ]);

        $assessment->refresh();
        $existingQuestions = AssessmentQuestion::query()
            ->where('assessment_id', $assessment->id)
            ->orderBy('order')
            ->get();

        $updatePayload = [
            'title' => ['vi' => 'Bài đánh giá ngủ (cập nhật)'],
            'description' => ['vi' => 'Mô tả mới'],
            // Use 'active' as the published status in this app.
            'status' => 'active',
            'questions' => $existingQuestions->map(function (AssessmentQuestion $q) {
                return [
                    'order' => $q->order,
                    'type' => $q->type,
                    'content' => ['vi' => (string) $q->getTranslation('content', 'vi')],
                    'options' => $q->options->map(function ($opt) {
                        return [
                            'content' => ['vi' => (string) $opt->getTranslation('content', 'vi')],
                            'score' => (int) $opt->score,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];

        $res = $this->actingAs($consultant)->put("/en/consultant/assessments/{$assessment->id}", $updatePayload);
        $res->assertStatus(302);

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'status' => 'active',
        ]);

        $countBefore = Assessment::query()->count();
        $res = $this->actingAs($consultant)->delete("/en/consultant/assessments/{$assessment->id}");
        $res->assertStatus(302);
        $this->assertSame($countBefore - 1, Assessment::query()->count());

        $otherConsultant = User::factory()->create(['role' => 'consultant']);

        $otherAssessment = Assessment::query()->create([
            'title' => ['vi' => 'Của người khác'],
            'description' => ['vi' => '...'],
            'status' => 'created',
            'created_by' => $otherConsultant->id,
        ]);

        $updateOtherPayload = [
            'title' => ['vi' => 'Hack'],
            'description' => ['vi' => 'Hack'],
            'status' => 'active',
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
        ];

        $this->actingAs($consultant)
            ->put("/en/consultant/assessments/{$otherAssessment->id}", $updateOtherPayload)
            ->assertForbidden();

        $res = $this->actingAs($consultant)
            ->delete("/en/consultant/assessments/{$otherAssessment->id}");
            
        if ($res->status() === 302) {
            dump("Redirecting to: " . $res->headers->get('Location'));
        }
        
        $res->assertForbidden();
    }

    public function test_consultant_can_crud_daily_missions_with_tags_and_cannot_delete_others(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);

        $create = $this->actingAs($consultant)->post('/en/consultant/daily-missions', [
            'title' => 'Mission 1',
            'description' => 'Desc',
            'points' => 10,
            'is_body' => 1,
            'is_mind' => 0,
        ]);
        $create->assertStatus(302);

        $mission = DailyMission::query()->where('created_by_id', $consultant->id)->latest('id')->firstOrFail();

        $this->assertDatabaseHas('daily_missions', [
            'id' => $mission->id,
            'created_by_id' => $consultant->id,
            'is_body' => 1,
            'is_mind' => 0,
        ]);

        $update = $this->actingAs($consultant)->put("/en/consultant/daily-missions/{$mission->id}", [
            'title' => 'Mission 1',
            'description' => 'Desc',
            'points' => 10,
            'is_body' => 1,
            'is_mind' => 0,
            'is_wisdom' => 1,
        ]);
        $update->assertStatus(302);

        $this->assertDatabaseHas('daily_missions', [
            'id' => $mission->id,
            'is_wisdom' => 1,
        ]);

        $delete = $this->actingAs($consultant)->delete("/en/consultant/daily-missions/{$mission->id}");
        $delete->assertStatus(302);
        $this->assertDatabaseMissing('daily_missions', ['id' => $mission->id]);

        $otherConsultant = User::factory()->create(['role' => 'consultant']);
        $otherMission = DailyMission::query()->create([
            'title' => ['en' => 'Other'],
            'description' => ['en' => '...'],
            'points' => 5,
            'created_by_id' => $otherConsultant->id,
        ]);

        $this->actingAs($consultant)
            ->delete("/en/consultant/daily-missions/{$otherMission->id}")
            ->assertForbidden();
    }

    public function test_consultant_can_create_and_delete_video(): void
    {
        $this->withoutExceptionHandling();

        $consultant = User::factory()->create(['role' => 'consultant']);

        $create = $this->actingAs($consultant)->post('/en/consultant/videos', [
            'title' => 'Video 1',
            'url' => 'https://www.youtube.com/watch?v=abc123',
            'language' => 'vi',
            'pillar_tags' => ['body'],
            'source_tags' => ['science'],
            'is_active' => 1,
        ]);
        $create->assertStatus(302);

        $video = Video::query()->where('url', 'https://www.youtube.com/watch?v=abc123')->latest('id')->firstOrFail();

        $this->assertEquals('Video 1', $video->title);
        $this->assertEquals('vi', $video->language);

        $fresh = Video::query()->findOrFail($video->id);
        $this->assertSame(['body'], $fresh->pillar_tags);
        $this->assertSame(['science'], $fresh->source_tags);

        $delete = $this->actingAs($consultant)->delete("/en/consultant/videos/{$video->id}");
        $delete->assertStatus(302);
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }
}
