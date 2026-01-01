<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Services\AssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FlexibleScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_flexible_scoring_calculates_total_score_and_result_label(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create an assessment with score ranges
        $assessment = Assessment::create([
            'title' => ['en' => 'Flexible Test Assessment'],
            'description' => ['en' => 'Test Description'],
            'status' => 'active',
            'created_by' => $user->id,
            'score_ranges' => [
                ['min' => 0, 'max' => 10, 'label' => 'Low'],
                ['min' => 11, 'max' => 20, 'label' => 'Medium'],
                ['min' => 21, 'max' => 30, 'label' => 'High'],
            ],
        ]);

        // Create a single choice question with scored options
        $question1 = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => 'single_choice',
            'content' => ['en' => 'How do you feel?'],
            'order' => 1,
        ]);

        // Create options with different scores
        $option1 = AssessmentOption::create([
            'question_id' => $question1->id,
            'content' => ['en' => 'Bad'],
            'score' => 0,
        ]);

        $option2 = AssessmentOption::create([
            'question_id' => $question1->id,
            'content' => ['en' => 'Good'],
            'score' => 15,
        ]);

        // Create another single choice question (text questions not supported yet)
        $question2 = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => 'single_choice',
            'content' => ['en' => 'How is your sleep?'],
            'order' => 2,
        ]);

        $option3 = AssessmentOption::create([
            'question_id' => $question2->id,
            'content' => ['en' => 'Poor'],
            'score' => 5,
        ]);

        $option4 = AssessmentOption::create([
            'question_id' => $question2->id,
            'content' => ['en' => 'Excellent'],
            'score' => 10,
        ]);

        // Test answers
        $answers = [
            $question1->id => $option2->id, // Select "Good" option (15 points)
            $question2->id => $option4->id, // Select "Excellent" option (10 points)
        ];

        // Test the flexible scoring
        $service = app(AssessmentService::class);
        $result = $service->calculateFlexibleScore($answers, $assessment);

        // Assertions
        $this->assertSame(25, $result['total_score']); // 15 + 10 = 25
        $this->assertSame('High', $result['result_label']); // 25 falls in 21-30 range
        $this->assertArrayHasKey('scored_answers', $result);
        $this->assertArrayHasKey('text_answers', $result);
        $this->assertCount(2, $result['scored_answers']);
        $this->assertEmpty($result['text_answers']); // No text answers in this test
        $this->assertSame(15, $result['scored_answers'][$question1->id]['score']);
        $this->assertSame(10, $result['scored_answers'][$question2->id]['score']);
    }

    public function test_flexible_scoring_handles_multi_choice_questions(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create an assessment with score ranges
        $assessment = Assessment::create([
            'title' => ['en' => 'Multi Choice Test'],
            'description' => ['en' => 'Test Description'],
            'status' => 'active',
            'created_by' => $user->id,
            'score_ranges' => [
                ['min' => 0, 'max' => 5, 'label' => 'Low'],
                ['min' => 6, 'max' => 15, 'label' => 'Medium'],
                ['min' => 16, 'max' => 25, 'label' => 'High'],
            ],
        ]);

        // Create a multi-choice question
        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => 'multi_choice',
            'content' => ['en' => 'Select all that apply'],
            'order' => 1,
        ]);

        // Create options with scores
        $option1 = AssessmentOption::create([
            'question_id' => $question->id,
            'content' => ['en' => 'Option 1'],
            'score' => 5,
        ]);

        $option2 = AssessmentOption::create([
            'question_id' => $question->id,
            'content' => ['en' => 'Option 2'],
            'score' => 10,
        ]);

        $option3 = AssessmentOption::create([
            'question_id' => $question->id,
            'content' => ['en' => 'Option 3'],
            'score' => 3,
        ]);

        // Test multi-choice answers (select multiple options)
        $answers = [
            $question->id => [$option1->id, $option2->id], // Select Option 1 and 2 (5 + 10 = 15 points)
        ];

        // Test the flexible scoring
        $service = app(AssessmentService::class);
        $result = $service->calculateFlexibleScore($answers, $assessment);

        // Assertions
        $this->assertSame(15, $result['total_score']); // 5 + 10 = 15
        $this->assertSame('Medium', $result['result_label']); // 15 falls in 11-20 range
        $this->assertCount(2, $result['scored_answers'][$question->id]);
    }
}
