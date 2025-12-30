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
            // Legacy storage maps canonical 'user' to 'member'.
            'role' => 'member',
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

        UserTree::query()->create([
            'user_id' => $user->id,
            'season' => 'winter',
            'health' => 80,
            'exp' => 250,
            'fruits_balance' => 10,
            'total_fruits_given' => 5,
        ]);

        /** @var AdminService $service */
        $service = app(AdminService::class);
        $service->resetAssessment($user->id);

        $this->assertDatabaseMissing('user_quiz_results', [
            'user_id' => $user->id,
        ]);

        $user->refresh();
        $this->assertSame('new', $user->onboarding_status);

        $tree = UserTree::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame('spring', $tree->season);
        $this->assertSame(0, $tree->health);
        $this->assertSame(0, (int) $tree->exp);
        $this->assertSame(0, (int) $tree->fruits_balance);
        $this->assertSame(0, (int) $tree->total_fruits_given);
    }

    public function test_assessment_service_calculate_score_sums_scores_and_sets_custom_focus(): void
    {
        $questionIds = [];

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['heart'][] = \App\Models\AssessmentQuestion::query()->create([
                'content' => ['en' => "Heart {$i}"],
                'pillar_group' => 'heart',
                'order' => $i,
            ])->id;
        }

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['grit'][] = \App\Models\AssessmentQuestion::query()->create([
                'content' => ['en' => "Grit {$i}"],
                'pillar_group' => 'grit',
                'order' => 10 + $i,
            ])->id;
        }

        for ($i = 1; $i <= 10; $i++) {
            $questionIds['wisdom'][] = \App\Models\AssessmentQuestion::query()->create([
                'content' => ['en' => "Wisdom {$i}"],
                'pillar_group' => 'wisdom',
                'order' => 20 + $i,
            ])->id;
        }

        $answers = [];
        foreach ($questionIds['heart'] as $id) {
            $answers[$id] = 5;
        }
        foreach ($questionIds['grit'] as $id) {
            $answers[$id] = 3;
        }
        foreach ($questionIds['wisdom'] as $id) {
            $answers[$id] = 4;
        }

        /** @var AssessmentService $service */
        $service = app(AssessmentService::class);
        $result = $service->calculateScore($answers);

        $this->assertSame(50, $result['heart_score']);
        $this->assertSame(30, $result['grit_score']);
        $this->assertSame(40, $result['wisdom_score']);
        $this->assertSame('grit', $result['custom_focus']);
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
