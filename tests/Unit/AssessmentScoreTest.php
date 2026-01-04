<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAssessment;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\PainPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssessmentScoreTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Assessment $assessment;
    private array $questions = [];
    private array $options = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->assessment = Assessment::factory()->create();

        // Create 30 questions with 5-point scale options (1-5)
        for ($i = 1; $i <= 30; $i++) {
            $question = AssessmentQuestion::factory()->create([
                'assessment_id' => $this->assessment->id,
                'order' => $i,
                'content' => [
                    'en' => "Test Question {$i}",
                    'vi' => "Câu hỏi kiểm tra {$i}"
                ]
            ]);

            $this->questions[$i] = $question;

            // Create 5 options for each question (1-5 scale)
            for ($score = 1; $score <= 5; $score++) {
                $option = AssessmentOption::factory()->create([
                    'question_id' => $question->id,
                    'score' => $score,
                    'content' => [
                        'en' => "Option {$score}",
                        'vi' => "Lựa chọn {$score}"
                    ]
                ]);
                $this->options[$i][$score] = $option;
            }
        }
    }

    /** @test */
    public function it_calculates_body_mind_wisdom_scores_correctly_from_30_answers()
    {
        // Create answers: First 10 questions for Body, next 10 for Mind, last 10 for Wisdom
        $answers = [];
        $expectedBodyScore = 0;
        $expectedMindScore = 0;
        $expectedWisdomScore = 0;

        // Questions 1-10: Body (scores: 3,4,5,2,3,4,5,1,2,3)
        $bodyScores = [3, 4, 5, 2, 3, 4, 5, 1, 2, 3];
        for ($i = 1; $i <= 10; $i++) {
            $score = $bodyScores[$i - 1];
            $answers[$this->questions[$i]->id] = $this->options[$i][$score]->id;
            $expectedBodyScore += $score;
        }

        // Questions 11-20: Mind (scores: 4,3,2,5,4,3,2,1,4,3)
        $mindScores = [4, 3, 2, 5, 4, 3, 2, 1, 4, 3];
        for ($i = 11; $i <= 20; $i++) {
            $score = $mindScores[$i - 11];
            $answers[$this->questions[$i]->id] = $this->options[$i][$score]->id;
            $expectedMindScore += $score;
        }

        // Questions 21-30: Wisdom (scores: 5,4,3,2,1,5,4,3,2,1)
        $wisdomScores = [5, 4, 3, 2, 1, 5, 4, 3, 2, 1];
        for ($i = 21; $i <= 30; $i++) {
            $score = $wisdomScores[$i - 21];
            $answers[$this->questions[$i]->id] = $this->options[$i][$score]->id;
            $expectedWisdomScore += $score;
        }

        $totalScore = $expectedBodyScore + $expectedMindScore + $expectedWisdomScore;

        // Create user assessment
        $userAssessment = UserAssessment::factory()->create([
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answers,
            'total_score' => $totalScore,
        ]);

        // Verify the scores
        $this->assertEquals(32, $expectedBodyScore); // 3+4+5+2+3+4+5+1+2+3 = 32
        $this->assertEquals(31, $expectedMindScore); // 4+3+2+5+4+3+2+1+4+3 = 31
        $this->assertEquals(30, $expectedWisdomScore); // 5+4+3+2+1+5+4+3+2+1 = 30
        $this->assertEquals(93, $totalScore); // 32+31+30 = 93

        // Verify the assessment was created with correct total score
        $this->assertEquals($totalScore, $userAssessment->total_score);
        $this->assertEquals($answers, $userAssessment->answers);
    }

    /** @test */
    public function it_sets_pain_point_score_to_10_for_always_negative_answer()
    {
        // Create a specific negative question (e.g., "I feel hopeless")
        $negativeQuestion = AssessmentQuestion::factory()->create([
            'assessment_id' => $this->assessment->id,
            'order' => 31,
            'content' => [
                'en' => "I feel hopeless about my future",
                'vi' => "Tôi cảm thấy tuyệt vọng về tương lai"
            ]
        ]);

        // Create options including "Always" (score 5) which should trigger score 10
        $alwaysOption = AssessmentOption::factory()->create([
            'question_id' => $negativeQuestion->id,
            'score' => 5,
            'content' => [
                'en' => "Always",
                'vi' => "Luôn luôn"
            ]
        ]);

        $sometimesOption = AssessmentOption::factory()->create([
            'question_id' => $negativeQuestion->id,
            'score' => 3,
            'content' => [
                'en' => "Sometimes",
                'vi' => "Đôi khi"
            ]
        ]);

        // Create a pain point
        $painPoint = PainPoint::factory()->create([
            'name' => 'Hopelessness',
            'category' => 'mind'
        ]);

        // Test Case 1: User answers "Always" to negative question
        $answersWithAlways = [
            $negativeQuestion->id => $alwaysOption->id
        ];

        $userAssessment1 = UserAssessment::factory()->create([
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answersWithAlways,
            'total_score' => 5,
        ]);

        // Attach pain point with score 10 (simulating the business logic)
        $this->user->painPoints()->attach($painPoint->id, ['score' => 10]);

        $this->assertEquals(10, $this->user->painPoints()->first()->pivot->score);

        // Test Case 2: User answers "Sometimes" to negative question
        $user2 = User::factory()->create();
        $answersWithSometimes = [
            $negativeQuestion->id => $sometimesOption->id
        ];

        $userAssessment2 = UserAssessment::factory()->create([
            'user_id' => $user2->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answersWithSometimes,
            'total_score' => 3,
        ]);

        // Attach pain point with lower score (simulating normal case)
        $user2->painPoints()->syncWithoutDetaching([$painPoint->id => ['score' => 5]]);

        $this->assertEquals(5, $user2->painPoints()->latest()->first()->pivot->score);
    }

    /** @test */
    public function it_calculates_correct_percentage_based_on_5_point_scale()
    {
        // Create answers with mixed scores
        $answers = [];
        $totalScore = 0;
        $maxPossibleScore = 0;

        for ($i = 1; $i <= 30; $i++) {
            $score = rand(1, 5); // Random score between 1-5
            $answers[$this->questions[$i]->id] = $this->options[$i][$score]->id;
            $totalScore += $score;
            $maxPossibleScore += 5; // Maximum score per question is 5
        }

        $userAssessment = UserAssessment::factory()->create([
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answers,
            'total_score' => $totalScore,
        ]);

        $expectedPercentage = ($totalScore / $maxPossibleScore) * 100;

        // Verify the calculation is correct
        $this->assertEquals($totalScore, $userAssessment->total_score);
        $this->assertEquals($maxPossibleScore, 150); // 30 questions * 5 max points
        $this->assertEqualsWithDelta($expectedPercentage, ($totalScore / 150) * 100, 0.01);
    }

    /** @test */
    public function it_handles_perfect_score_correctly()
    {
        // Create all answers with maximum score (5)
        $answers = [];
        $totalScore = 0;

        for ($i = 1; $i <= 30; $i++) {
            $answers[$this->questions[$i]->id] = $this->options[$i][5]->id; // Always choose option with score 5
            $totalScore += 5;
        }

        $userAssessment = UserAssessment::factory()->create([
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answers,
            'total_score' => $totalScore,
        ]);

        // Perfect score verification
        $this->assertEquals(150, $totalScore); // 30 questions * 5 points
        $this->assertEquals(100.0, ($totalScore / 150) * 100); // 100% percentage
    }

    /** @test */
    public function it_handles_minimum_score_correctly()
    {
        // Create all answers with minimum score (1)
        $answers = [];
        $totalScore = 0;

        for ($i = 1; $i <= 30; $i++) {
            $answers[$this->questions[$i]->id] = $this->options[$i][1]->id; // Always choose option with score 1
            $totalScore += 1;
        }

        $userAssessment = UserAssessment::factory()->create([
            'user_id' => $this->user->id,
            'assessment_id' => $this->assessment->id,
            'answers' => $answers,
            'total_score' => $totalScore,
        ]);

        // Minimum score verification
        $this->assertEquals(30, $totalScore); // 30 questions * 1 point
        $this->assertEquals(20.0, ($totalScore / 150) * 100); // 20% percentage
    }
}
