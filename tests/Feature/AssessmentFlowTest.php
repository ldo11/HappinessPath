<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_deep_self_discovery_assessment()
    {
        // 1. Create a user
        $user = User::factory()->create();

        // 2. Create the "Deep Self-Discovery" assessment (30 questions)
        // We simulate this by creating an active assessment with questions.
        $assessment = Assessment::create([
            'title' => ['en' => 'Deep Self-Discovery'],
            'description' => ['en' => 'A deep dive into your soul.'],
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create 30 questions to match the scenario
        for ($i = 1; $i <= 30; $i++) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => ['en' => "Question $i"],
                'type' => 'single_choice',
                'order' => $i,
            ]);

            AssessmentOption::create([
                'question_id' => $question->id,
                'content' => ['en' => 'Option A'],
                'score' => 1,
            ]);
            AssessmentOption::create([
                'question_id' => $question->id,
                'content' => ['en' => 'Option B'],
                'score' => 2,
            ]);
        }

        // 3. Act as User -> Visit Assessment Index/Show page
        $this->actingAs($user);

        // Check Index
        $responseIndex = $this->get(route('user.assessments.index', ['locale' => 'en']));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Deep Self-Discovery');
        $responseIndex->assertSee('30 questions'); // Verify count is displayed correctly

        // Check Show
        $responseShow = $this->get(route('user.assessments.show', ['locale' => 'en', 'assessment' => $assessment->id]));
        $responseShow->assertOk();
        $responseShow->assertSee('Deep Self-Discovery');
        $responseShow->assertSee('Question 1');
        $responseShow->assertSee('Question 30');
    }
}
