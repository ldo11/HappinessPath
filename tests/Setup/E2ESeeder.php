<?php

namespace Tests\Setup;

use App\Models\Language;
use App\Models\Solution;
use App\Models\SolutionTranslation;
use App\Models\User;
use App\Models\UserTree;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class E2ESeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLanguages();
        $this->seedUsers();
        $this->seedAssessmentQuestions();
        $this->seedTranslatorQueue();
    }

    private function seedLanguages(): void
    {
        $languages = [
            ['code' => 'vi', 'name' => 'Vietnamese', 'is_active' => true, 'is_default' => true],
            ['code' => 'en', 'name' => 'English', 'is_active' => true, 'is_default' => false],
            ['code' => 'de', 'name' => 'German', 'is_active' => true, 'is_default' => false],
            ['code' => 'fr', 'name' => 'French', 'is_active' => true, 'is_default' => false],
        ];

        foreach ($languages as $lang) {
            Language::query()->updateOrCreate(['code' => $lang['code']], $lang);
        }
    }

    private function seedUsers(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'E2E Admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'onboarding_status' => 'test_completed',
            ]
        );

        $translator = User::query()->updateOrCreate(
            ['email' => 'volunteer@test.com'],
            [
                'name' => 'E2E Volunteer',
                'password' => Hash::make('123456'),
                'role' => 'translator',
                'email_verified_at' => now(),
                'onboarding_status' => 'test_completed',
            ]
        );

        UserTree::query()->firstOrCreate(
            ['user_id' => $translator->id],
            [
                'season' => 'spring',
                'health' => 50,
                'exp' => 0,
                'fruits_balance' => 0,
                'total_fruits_given' => 0,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'E2E New User',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'email_verified_at' => now(),
                'onboarding_status' => 'new',
            ]
        );
    }

    private function seedAssessmentQuestions(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('assessment_answers')->truncate();
        DB::table('assessment_questions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $pillarById = function (int $id): string {
            if ($id <= 10) {
                return 'heart';
            }
            if ($id <= 20) {
                return 'grit';
            }
            return 'wisdom';
        };

        for ($i = 1; $i <= 30; $i++) {
            DB::table('assessment_questions')->insert([
                'id' => $i,
                'content' => json_encode(['vi' => "Câu hỏi {$i}", 'en' => "Question {$i}"]),
                'pillar_group' => $pillarById($i),
                'order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedTranslatorQueue(): void
    {
        $solution = Solution::query()->create([
            'type' => 'article',
            'url' => 'https://example.com/e2e-solution',
            'author_name' => 'E2E',
            'pillar_tag' => 'heart',
            'locale' => 'vi',
        ]);

        SolutionTranslation::query()->create([
            'solution_id' => $solution->id,
            'locale' => 'vi',
            'title' => 'Bài viết gốc (VI)',
            'content' => 'Nội dung gốc',
            'is_auto_generated' => false,
        ]);

        SolutionTranslation::query()->create([
            'solution_id' => $solution->id,
            'locale' => 'en',
            'title' => 'Auto EN Title',
            'content' => 'Auto EN Content',
            'is_auto_generated' => true,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'ai_provider' => 'e2e',
        ]);
    }
}
