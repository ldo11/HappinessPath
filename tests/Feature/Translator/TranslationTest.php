<?php

namespace Tests\Feature\Translator;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\LanguageLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translator_can_update_language_line_matrix_value(): void
    {
        $translator = User::factory()->create(['role' => 'translator']);

        $line = LanguageLine::query()->create([
            'group' => 'menu',
            'key' => 'home',
            'text' => ['en' => 'Home', 'vi' => ''],
        ]);

        $res = $this->actingAs($translator)->post("/en/translator/ui-matrix/{$line->id}", [
            'vi' => 'Trang chủ',
        ], [
            'Accept' => 'application/json',
        ]);

        $res->assertOk();

        $this->assertDatabaseHas('language_lines', [
            'id' => $line->id,
        ]);

        $line->refresh();
        $this->assertSame('Trang chủ', $line->text['vi'] ?? null);
    }

    public function test_translator_can_view_assessment_translation_page_and_submit_translations(): void
    {
        $translator = User::factory()->create(['role' => 'translator']);
        $consultant = User::factory()->create(['role' => 'consultant']);

        $assessment = Assessment::query()->create([
            'title' => ['en' => 'Sleep Quality'],
            'description' => ['en' => 'English description'],
            'status' => 'created',
            'created_by' => $consultant->id,
        ]);

        $question = AssessmentQuestion::query()->create([
            'assessment_id' => $assessment->id,
            'content' => ['en' => 'Do you sleep well?'],
            'type' => 'single_choice',
            'order' => 1,
        ]);

        $view = $this->actingAs($translator)->get("/translator/assessments/{$assessment->id}/translate");
        $view->assertOk();

        $submit = $this->actingAs($translator)->post("/translator/assessments/{$assessment->id}/submit-translation", [
            'title' => [
                'vi' => 'Chất lượng giấc ngủ',
            ],
            'questions' => [
                $question->id => [
                    'content' => [
                        'vi' => 'Bạn ngủ ngon không?',
                    ],
                ],
            ],
        ], [
            'Accept' => 'application/json',
        ]);

        $submit->assertOk();

        $assessment->refresh();
        $question->refresh();

        $this->assertSame('Chất lượng giấc ngủ', $assessment->getTranslation('title', 'vi'));
        $this->assertSame('Bạn ngủ ngon không?', $question->getTranslation('content', 'vi'));
    }

    public function test_translator_cannot_delete_assessment(): void
    {
        $translator = User::factory()->create(['role' => 'translator']);
        $admin = User::factory()->create(['role' => 'admin']);

        $assessment = Assessment::query()->create([
            'title' => ['en' => 'Should not delete'],
            'description' => ['en' => '...'],
            'status' => 'created',
            'created_by' => $admin->id,
        ]);

        // Translator role should be blocked by the admin middleware on /admin/*
        $this->actingAs($translator)
            ->delete("/admin/assessments/{$assessment->id}")
            ->assertForbidden();
    }
}
