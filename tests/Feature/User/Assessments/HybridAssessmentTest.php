<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\UserAssessment;
use App\Models\ConsultationThread;
use App\Services\AssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HybridAssessmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $consultant;
    private User $regularUser;
    private Assessment $hybridAssessment;
    private array $questions = [];
    private array $options = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->consultant = User::factory()->create(['role' => 'consultant']);
        $this->regularUser = User::factory()->create(['role' => 'user']);

        $this->hybridAssessment = Assessment::factory()->create([
            'title' => ['en' => 'Sleep Quality Test', 'vi' => 'Kiểm tra Chất lượng Giấc ngủ'],
            'description' => ['en' => 'Assessment for sleep quality analysis', 'vi' => 'Đánh giá chất lượng giấc ngủ'],
            'status' => 'active',
            'created_by' => $this->admin->id,
            'score_ranges' => [
                ['min' => 0, 'max' => 10, 'label' => 'Healthy'],
                ['min' => 11, 'max' => 20, 'label' => 'Insomnia Alert'],
                ['min' => 21, 'max' => 30, 'label' => 'Severe Insomnia'],
            ],
        ]);

        $this->createHybridQuestions();
    }

    private function createHybridQuestions(): void
    {
        $q1 = AssessmentQuestion::factory()->create([
            'assessment_id' => $this->hybridAssessment->id,
            'type' => 'single_choice',
            'content' => ['en' => 'Do you use phone before bed?', 'vi' => 'Bạn có sử dụng điện thoại trước khi ngủ không?'],
            'order' => 1,
        ]);

        $q1OptionA = AssessmentOption::factory()->create([
            'question_id' => $q1->id,
            'content' => ['en' => 'No', 'vi' => 'Không'],
            'score' => 0,
        ]);

        $q1OptionB = AssessmentOption::factory()->create([
            'question_id' => $q1->id,
            'content' => ['en' => 'Yes', 'vi' => 'Có'],
            'score' => 10,
        ]);

        $q2 = AssessmentQuestion::factory()->create([
            'assessment_id' => $this->hybridAssessment->id,
            'type' => 'single_choice',
            'content' => ['en' => 'What keeps you awake?', 'vi' => 'Điều gì khiến bạn thức?'],
            'order' => 2,
        ]);

        $q2OptionA = AssessmentOption::factory()->create([
            'question_id' => $q2->id,
            'content' => ['en' => 'Stress about work', 'vi' => 'Căng thẳng về công việc'],
            'score' => 0,
        ]);

        $q3 = AssessmentQuestion::factory()->create([
            'assessment_id' => $this->hybridAssessment->id,
            'type' => 'multi_choice',
            'content' => ['en' => 'Symptoms?', 'vi' => 'Triệu chứng?'],
            'order' => 3,
        ]);

        $q3OptionA = AssessmentOption::factory()->create([
            'question_id' => $q3->id,
            'content' => ['en' => 'Headache', 'vi' => 'Đau đầu'],
            'score' => 5,
        ]);

        $q3OptionB = AssessmentOption::factory()->create([
            'question_id' => $q3->id,
            'content' => ['en' => 'Sweating', 'vi' => 'Ra mồ hôi'],
            'score' => 2,
        ]);

        $this->questions = [
            'single_choice' => $q1,
            'text' => $q2,
            'multi_choice' => $q3,
        ];

        $this->options = [
            'q1_no' => $q1OptionA,
            'q1_yes' => $q1OptionB,
            'q2_text' => $q2OptionA,
            'q3_headache' => $q3OptionA,
            'q3_sweating' => $q3OptionB,
        ];
    }

    /** @test */
    public function test_calculates_mixed_score_correctly(): void
    {
        $answers = [
            $this->questions['single_choice']->id => $this->options['q1_yes']->id,
            $this->questions['text']->id => $this->options['q2_text']->id,
            $this->questions['multi_choice']->id => [$this->options['q3_headache']->id],
        ];

        $service = app(AssessmentService::class);
        $result = $service->calculateFlexibleScore($answers, $this->hybridAssessment);

        $this->assertSame(15, $result['total_score']);
        $this->assertSame('Insomnia Alert', $result['result_label']);
        $this->assertArrayHasKey('scored_answers', $result);
        $this->assertArrayHasKey('text_answers', $result);
        $this->assertCount(3, $result['scored_answers']);
        $this->assertCount(0, $result['text_answers']);
        $this->assertSame(10, $result['scored_answers'][$this->questions['single_choice']->id]['score']);
        $this->assertSame(5, $result['scored_answers'][$this->questions['multi_choice']->id][0]['score']);
    }

    /** @test */
    public function test_stores_text_answers_but_ignores_in_score(): void
    {
        $answers = [
            $this->questions['text']->id => $this->options['q2_text']->id,
        ];

        $service = app(AssessmentService::class);
        $result = $service->calculateFlexibleScore($answers, $this->hybridAssessment);

        $this->assertSame(0, $result['total_score']);
        $this->assertSame('Healthy', $result['result_label']);
        $this->assertArrayHasKey('scored_answers', $result);
        $this->assertArrayHasKey($this->questions['text']->id, $result['scored_answers']);
        $this->assertSame(0, $result['scored_answers'][$this->questions['text']->id]['score']);
        $this->assertArrayHasKey('text_answers', $result);
        $this->assertCount(0, $result['text_answers']);
    }

    /** @test */
    public function test_score_range_boundary_logic(): void
    {
        $service = app(AssessmentService::class);

        $answersUpperBoundary = [
            $this->questions['single_choice']->id => $this->options['q1_yes']->id,
        ];

        $resultUpper = $service->calculateFlexibleScore($answersUpperBoundary, $this->hybridAssessment);
        $this->assertSame(10, $resultUpper['total_score']);
        $this->assertSame('Healthy', $resultUpper['result_label']);

        $tempOption = AssessmentOption::factory()->create([
            'question_id' => $this->questions['single_choice']->id,
            'content' => ['en' => 'Sometimes', 'vi' => 'Thỉ đôi khi'],
            'score' => 1,
        ]);

        $answersLowerBoundary = [
            $this->questions['single_choice']->id => $tempOption->id,
            $this->questions['multi_choice']->id => [$this->options['q3_headache']->id],
        ];

        $additionalOption = AssessmentOption::factory()->create([
            'question_id' => $this->questions['multi_choice']->id,
            'content' => ['en' => 'Anxiety', 'vi' => 'Lo âu lo'],
            'score' => 5,
        ]);

        $answersLowerBoundary[$this->questions['multi_choice']->id][] = $additionalOption->id;

        $resultLower = $service->calculateFlexibleScore($answersLowerBoundary, $this->hybridAssessment);
        $this->assertSame(11, $resultLower['total_score']);
        $this->assertSame('Insomnia Alert', $resultLower['result_label']);
    }

    /** @test */
    public function test_consultant_can_see_hybrid_details(): void
    {
        $answers = [
            $this->questions['single_choice']->id => $this->options['q1_yes']->id,
            $this->questions['text']->id => $this->options['q2_text']->id,
            $this->questions['multi_choice']->id => [$this->options['q3_headache']->id],
        ];

        $service = app(AssessmentService::class);
        $result = $service->calculateFlexibleScore($answers, $this->hybridAssessment);

        $userAssessment = UserAssessment::create([
            'user_id' => $this->regularUser->id,
            'assessment_id' => $this->hybridAssessment->id,
            'submission_mode' => 'submitted_for_consultation',
            'answers' => $answers,
            'total_score' => $result['total_score'],
            'result_label' => $result['result_label'],
            'consultation_thread_id' => null,
        ]);

        $consultationThread = ConsultationThread::create([
            'user_id' => $this->regularUser->id,
            'title' => 'Sleep Quality Consultation',
            'content' => 'User submitted sleep quality assessment for consultation',
            'related_assessment_id' => $this->hybridAssessment->id,
            'status' => 'open',
            'is_private' => true,
        ]);

        $userAssessment->consultation_thread_id = $consultationThread->id;
        $userAssessment->save();

        $consultantResult = UserAssessment::with(['assessment', 'user'])
            ->where('id', $userAssessment->id)
            ->first();

        $this->assertNotNull($consultantResult);
        $this->assertSame(15, $consultantResult->total_score);
        $this->assertSame('Insomnia Alert', $consultantResult->result_label);
        $this->assertIsArray($consultantResult->answers);
        $this->assertArrayHasKey($this->questions['text']->id, $consultantResult->answers);
        $this->assertSame($this->options['q2_text']->id, $consultantResult->answers[$this->questions['text']->id]);
    }

    /** @test */
    public function test_hybrid_assessment_factory_creates_correct_structure(): void
    {
        $this->assertSame('Sleep Quality Test', $this->hybridAssessment->title);
        $this->assertIsArray($this->hybridAssessment->score_ranges);
        $this->assertCount(3, $this->hybridAssessment->score_ranges);

        $expectedRanges = [
            ['min' => 0, 'max' => 10, 'label' => 'Healthy'],
            ['min' => 11, 'max' => 20, 'label' => 'Insomnia Alert'],
            ['min' => 21, 'max' => 30, 'label' => 'Severe Insomnia'],
        ];

        foreach ($expectedRanges as $index => $expectedRange) {
            $this->assertSame($expectedRange, $this->hybridAssessment->score_ranges[$index]);
        }

        $this->assertCount(3, $this->hybridAssessment->questions);
        
        $singleChoice = $this->hybridAssessment->questions->where('type', 'single_choice')->first();
        $multiChoice = $this->hybridAssessment->questions->where('type', 'multi_choice')->first();
        
        $this->assertNotNull($singleChoice);
        $this->assertNotNull($multiChoice);
        $this->assertCount(2, $singleChoice->options);
        $this->assertCount(2, $multiChoice->options);
    }

    /** @test */
    public function test_result_label_calculation_edge_cases(): void
    {
        $service = app(AssessmentService::class);

        $resultBelow = $service->calculateFlexibleScore([], $this->hybridAssessment);
        $this->assertSame(0, $resultBelow['total_score']);
        $this->assertSame('Healthy', $resultBelow['result_label']);

        $highScoreOption = AssessmentOption::factory()->create([
            'question_id' => $this->questions['single_choice']->id,
            'content' => ['en' => 'Very High Score', 'vi' => 'Điểm rất cao'],
            'score' => 35,
        ]);

        $answersHigh = [
            $this->questions['single_choice']->id => $highScoreOption->id,
        ];

        $resultHigh = $service->calculateFlexibleScore($answersHigh, $this->hybridAssessment);
        $this->assertSame(35, $resultHigh['total_score']);
        $this->assertNull($resultHigh['result_label']);
    }
}
