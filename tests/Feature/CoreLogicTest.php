<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Solution;
use App\Models\SolutionTranslation;
use App\Models\User;
use App\Models\UserQuizResult;
use App\Models\UserTree;
use App\Services\AdminService;
use App\Services\AssessmentService;
use App\Services\TranslationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CoreLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_service_create_user_creates_user_hashes_password_and_dispatches_email_verification_event(): void
    {
        Event::fake();

        /** @var AdminService $service */
        $service = app(AdminService::class);

        $user = $service->createUser([
            'name' => 'Test Admin Created',
            'email' => 'created@test.com',
            'password' => '12345678',
            'role' => 'user',
            'city' => 'Test City',
            'spiritual_preference' => 'secular',
            'geo_privacy' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'created@test.com',
            // Role is now standardized to 'user'
            'role' => 'user',
        ]);

        $fresh = User::query()->findOrFail($user->id);
        $this->assertNotSame('12345678', $fresh->password);
        $this->assertTrue(Hash::check('12345678', $fresh->password));

        Event::assertDispatched(Registered::class, function (Registered $event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_admin_service_reset_assessment_deletes_quiz_results_resets_tree_and_sets_onboarding_status_to_new(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_status' => 'test_completed',
        ]);

        UserQuizResult::query()->create([
            'user_id' => $user->id,
            'heart_score' => 10,
            'grit_score' => 20,
            'wisdom_score' => 30,
            'dominant_issue' => 'heart',
        ]);

        // UserTree model has been removed, so we skip this part
        // The reset functionality should focus on quiz results and onboarding status

        /** @var AdminService $service */
        $service = app(AdminService::class);
        $service->resetAssessment($user->id);

        $this->assertDatabaseMissing('user_quiz_results', [
            'user_id' => $user->id,
        ]);

        $user->refresh();
        $this->assertSame('new', $user->onboarding_status);
    }

    public function test_assessment_service_calculate_score_sums_scores_and_sets_custom_focus(): void
    {
        // Create a test user first
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create a test assessment first
        $assessment = \App\Models\Assessment::create([
            'title' => ['en' => 'Test Assessment'],
            'description' => ['en' => 'Test Description'],
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $questionIds = [];

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['body'][] = \App\Models\AssessmentQuestion::query()->create([
                'assessment_id' => $assessment->id,
                'content' => ['en' => "Body {$i}"],
                'pillar_group_new' => 'body',
                'order' => $i,
            ])->id;
        }

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['mind'][] = \App\Models\AssessmentQuestion::query()->create([
                'assessment_id' => $assessment->id,
                'content' => ['en' => "Mind {$i}"],
                'pillar_group_new' => 'mind',
                'order' => 10 + $i,
            ])->id;
        }

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['wisdom'][] = \App\Models\AssessmentQuestion::query()->create([
                'assessment_id' => $assessment->id,
                'content' => ['en' => "Wisdom {$i}"],
                'pillar_group_new' => 'wisdom',
                'order' => 20 + $i,
            ])->id;
        }

        $answers = [];
        foreach ($questionIds['body'] as $id) {
            $answers[$id] = 5;
        }
        foreach ($questionIds['mind'] as $id) {
            $answers[$id] = 3;
        }
        foreach ($questionIds['wisdom'] as $id) {
            $answers[$id] = 4;
        }

        /** @var AssessmentService $service */
        $service = app(AssessmentService::class);
        $result = $service->calculateScore($answers);

        // Test new percentage-based scoring system
        // Body questions (score 5 each) -> Body: 100% (5/5 * 100)
        $this->assertSame(100.0, $result['body']);
        // Mind questions (score 3 each) -> Mind: 60% (3/5 * 100)  
        $this->assertSame(60.0, $result['mind']);
        // Wisdom questions (score 4 each) -> Wisdom: 80% (4/5 * 100)
        $this->assertSame(80.0, $result['wisdom']);
        
        // Check that pain point triggers are empty (no low scores)
        $this->assertEmpty($result['pain_point_triggers']);
    }

    public function test_assessment_service_triggers_pain_points_when_score_is_low(): void
    {
        // Create a test user first
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create a test assessment first
        $assessment = \App\Models\Assessment::create([
            'title' => ['en' => 'Test Assessment'],
            'description' => ['en' => 'Test Description'],
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create questions with pain point keys
        $question1 = \App\Models\AssessmentQuestion::query()->create([
            'assessment_id' => $assessment->id,
            'content' => ['en' => "Low Score Question"],
            'pillar_group_new' => 'body',
            'related_pain_point_key' => 'anxiety',
            'order' => 1,
        ]);

        $question2 = \App\Models\AssessmentQuestion::query()->create([
            'assessment_id' => $assessment->id,
            'content' => ['en' => "High Score Question"],
            'pillar_group_new' => 'mind',
            'related_pain_point_key' => 'stress',
            'order' => 2,
        ]);

        // Submit answers with low score for first question (should trigger pain point)
        $answers = [
            $question1->id => 2, // Low score - should trigger pain point
            $question2->id => 5, // High score - should not trigger pain point
        ];

        /** @var AssessmentService $service */
        $service = app(AssessmentService::class);
        $result = $service->calculateScore($answers);

        // Assert pain point was triggered for low score
        $this->assertArrayHasKey('pain_point_triggers', $result);
        $this->assertArrayHasKey('anxiety', $result['pain_point_triggers']);
        $this->assertEquals(2, $result['pain_point_triggers']['anxiety']);
        
        // Assert no pain point triggered for high score
        $this->assertArrayNotHasKey('stress', $result['pain_point_triggers']);
    }

    public function test_translation_service_get_missing_translations_returns_only_untranslated_solutions(): void
    {
        Language::query()->create([
            'code' => 'vi',
            'name' => 'Vietnamese',
            'is_active' => true,
            'is_default' => true,
        ]);
        Language::query()->create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
            'is_default' => false,
        ]);

        $missing = Solution::query()->create([
            'type' => 'article',
            'url' => 'https://example.com/a',
            'author_name' => 'Author',
            'pillar_tag' => 'heart',
            'locale' => 'vi',
        ]);

        $present = Solution::query()->create([
            'type' => 'article',
            'url' => 'https://example.com/b',
            'author_name' => 'Author',
            'pillar_tag' => 'grit',
            'locale' => 'vi',
        ]);

        SolutionTranslation::query()->create([
            'solution_id' => $missing->id,
            'locale' => 'vi',
            'title' => 'VI title',
            'content' => 'VI content',
            'is_auto_generated' => false,
        ]);

        SolutionTranslation::query()->create([
            'solution_id' => $present->id,
            'locale' => 'vi',
            'title' => 'VI title',
            'content' => 'VI content',
            'is_auto_generated' => false,
        ]);

        SolutionTranslation::query()->create([
            'solution_id' => $present->id,
            'locale' => 'en',
            'title' => 'EN title',
            'content' => 'EN content',
            'is_auto_generated' => true,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);

        /** @var TranslationService $service */
        $service = app(TranslationService::class);
        $results = $service->getMissingTranslations('en');

        $this->assertCount(1, $results);
        $this->assertSame($missing->id, $results->first()->id);
    }
}
