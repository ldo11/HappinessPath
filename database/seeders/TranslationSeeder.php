<?php

namespace Database\Seeders;

use App\Models\DailyMission;
use App\Models\LanguageLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TranslationSeeder extends Seeder
{
    private array $locales = ['en', 'vi', 'de', 'kr'];

    public function run(): void
    {
        $this->importJsonTranslations();
        $this->seedLanguageLines();
        $consultantId = $this->ensureConsultant();

        $this->seedScenarioAssessments($consultantId);
        $this->seedDailyMissions($consultantId);
    }

    /**
     * Import all JSON translations from lang/ directory into database
     */
    private function importJsonTranslations(): void
    {
        $langPath = database_path('../lang');
        $allTranslations = [];

        // Read all JSON files and collect translations
        foreach ($this->locales as $locale) {
            $jsonFile = $langPath . '/' . $locale . '.json';
            if (file_exists($jsonFile)) {
                $translations = json_decode(file_get_contents($jsonFile), true);
                if (is_array($translations)) {
                    $this->flattenTranslations($translations, $locale, $allTranslations);
                }
            }
        }

        // Insert into database
        foreach ($allTranslations as $key => $translations) {
            LanguageLine::query()->updateOrCreate(
                ['group' => 'json', 'key' => $key],
                ['text' => $translations]
            );
        }

        $this->command->info('Imported ' . count($allTranslations) . ' translation keys from JSON files.');
    }

    /**
     * Flatten nested translation arrays with dot notation
     */
    private function flattenTranslations(array $translations, string $locale, array &$allTranslations, string $prefix = ''): void
    {
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value)) {
                $this->flattenTranslations($value, $locale, $allTranslations, $fullKey);
            } else {
                $allTranslations[$fullKey][$locale] = $value;
            }
        }
    }

    private function seedLanguageLines(): void
    {
        $rows = [
            ['group' => 'ui', 'key' => 'dashboard', 'text' => ['en' => 'Dashboard', 'vi' => 'Bảng điều khiển', 'de' => 'Dashboard', 'kr' => '대시보드']],
            ['group' => 'ui', 'key' => 'login', 'text' => ['en' => 'Login', 'vi' => 'Đăng nhập', 'de' => 'Anmelden', 'kr' => '로그인']],
            ['group' => 'ui', 'key' => 'logout', 'text' => ['en' => 'Logout', 'vi' => 'Đăng xuất', 'de' => 'Abmelden', 'kr' => '로그아웃']],
            ['group' => 'ui', 'key' => 'save', 'text' => ['en' => 'Save', 'vi' => 'Lưu', 'de' => 'Speichern', 'kr' => '저장']],
            ['group' => 'ui', 'key' => 'cancel', 'text' => ['en' => 'Cancel', 'vi' => 'Hủy', 'de' => 'Abbrechen', 'kr' => '취소']],
            ['group' => 'ui', 'key' => 'back', 'text' => ['en' => 'Back', 'vi' => 'Quay lại', 'de' => 'Zurück', 'kr' => '뒤로']],
            ['group' => 'ui', 'key' => 'search', 'text' => ['en' => 'Search', 'vi' => 'Tìm kiếm', 'de' => 'Suchen', 'kr' => '검색']],
            ['group' => 'ui', 'key' => 'edit', 'text' => ['en' => 'Edit', 'vi' => 'Chỉnh sửa', 'de' => 'Bearbeiten', 'kr' => '편집']],
            ['group' => 'ui', 'key' => 'submit', 'text' => ['en' => 'Submit', 'vi' => 'Gửi', 'de' => 'Senden', 'kr' => '제출']],
            ['group' => 'ui', 'key' => 'assessment_matrix', 'text' => ['en' => 'Assessment Matrix', 'vi' => 'Ma trận đánh giá', 'de' => 'Bewertungsmatrix', 'kr' => '평가 매트릭스']],
            ['group' => 'ui', 'key' => 'my_skills', 'text' => ['en' => 'My Skills', 'vi' => 'Kỹ năng của tôi', 'de' => 'Meine Fähigkeiten', 'kr' => '내 기술']],
            ['group' => 'ui', 'key' => 'translator_panel', 'text' => ['en' => 'Translator Panel', 'vi' => 'Bảng Dịch thuật', 'de' => 'Übersetzerbereich', 'kr' => '번역가 패널']],
            ['group' => 'ui', 'key' => 'needs_translation', 'text' => ['en' => 'Needs translation', 'vi' => 'Cần dịch', 'de' => 'Übersetzung erforderlich', 'kr' => '번역 필요']],
            ['group' => 'ui', 'key' => 'original', 'text' => ['en' => 'Original', 'vi' => 'Bản gốc', 'de' => 'Original', 'kr' => '원문']],
            ['group' => 'ui', 'key' => 'translation', 'text' => ['en' => 'Translation', 'vi' => 'Bản dịch', 'de' => 'Übersetzung', 'kr' => '번역']],
        ];

        foreach ($rows as $row) {
            LanguageLine::query()->updateOrCreate(
                ['group' => $row['group'], 'key' => $row['key']],
                ['text' => $row['text']]
            );
        }
    }

    private function ensureConsultant(): int
    {
        $email = 'consultant@happiness.test';
        $existing = DB::table('users')->where('email', $email)->first();

        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update([
                'role' => 'consultant',
                'password' => Hash::make('password'),
                'updated_at' => now(),
            ]);

            return (int) $existing->id;
        }

        return (int) DB::table('users')->insertGetId([
            'name' => 'Test Consultant',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'consultant',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Create 5 scenario assessments, each with 2 questions and each question with 3 answers.
     * Structure aligns with Assessment -> Questions -> Answers.
     */
    private function seedScenarioAssessments(int $consultantId): void
    {
        // Make seeding idempotent - clear only assessments created by this consultant
        DB::table('assessment_options')->where('question_id', function($query) use ($consultantId) {
            $query->select('id')->from('assessment_questions')->where('assessment_id', function($subQuery) use ($consultantId) {
                $subQuery->select('id')->from('assessments')->where('created_by', $consultantId);
            });
        })->delete();
        
        DB::table('assessment_questions')->where('assessment_id', function($query) use ($consultantId) {
            $query->select('id')->from('assessments')->where('created_by', $consultantId);
        })->delete();
        
        DB::table('assessments')->where('created_by', $consultantId)->delete();

        $scenarios = [
            [
                'key' => 'A1_EN',
                'title' => ['en' => 'Stress Reset (EN Only)'],
                'description' => ['en' => 'A short assessment to evaluate stress triggers and coping strategies.'],
                'q' => [
                    ['en' => 'When you feel stressed, what is your first reaction?'],
                    ['en' => 'What helps you recover fastest after a hard day?'],
                ],
                'a' => [
                    [
                        ['en' => 'I get irritated quickly.'],
                        ['en' => 'I shut down and withdraw.'],
                        ['en' => 'I take a deep breath and focus.'],
                    ],
                    [
                        ['en' => 'A walk outside.'],
                        ['en' => 'Talking to a friend.'],
                        ['en' => 'Meditation or breathing exercises.'],
                    ],
                ],
            ],
            [
                'key' => 'A2_VI',
                'title' => ['vi' => 'Bình an nội tâm (Chỉ VI)'],
                'description' => ['vi' => 'Bài đánh giá ngắn để khám phá yếu tố gây căng thẳng và cách hồi phục.'],
                'q' => [
                    ['vi' => 'Khi bạn căng thẳng, phản ứng đầu tiên của bạn là gì?'],
                    ['vi' => 'Điều gì giúp bạn hồi phục nhanh nhất sau một ngày mệt mỏi?'],
                ],
                'a' => [
                    [
                        ['vi' => 'Tôi dễ bực bội.'],
                        ['vi' => 'Tôi im lặng và thu mình lại.'],
                        ['vi' => 'Tôi hít thở sâu và tập trung.'],
                    ],
                    [
                        ['vi' => 'Đi dạo ngoài trời.'],
                        ['vi' => 'Nói chuyện với bạn bè.'],
                        ['vi' => 'Thiền hoặc tập thở.'],
                    ],
                ],
            ],
            [
                'key' => 'A3_DE',
                'title' => ['de' => 'Innere Ruhe (Nur DE)'],
                'description' => ['de' => 'Ein kurzer Test, um Stressauslöser und Bewältigungsstrategien zu erkennen.'],
                'q' => [
                    ['de' => 'Wie reagieren Sie zuerst, wenn Sie Stress spüren?'],
                    ['de' => 'Was hilft Ihnen am schnellsten, sich nach einem schweren Tag zu erholen?'],
                ],
                'a' => [
                    [
                        ['de' => 'Ich werde schnell gereizt.'],
                        ['de' => 'Ich ziehe mich zurück und werde still.'],
                        ['de' => 'Ich atme tief durch und fokussiere mich.'],
                    ],
                    [
                        ['de' => 'Ein Spaziergang im Freien.'],
                        ['de' => 'Mit einem Freund sprechen.'],
                        ['de' => 'Meditation oder Atemübungen.'],
                    ],
                ],
            ],
            [
                'key' => 'A4_KR',
                'title' => ['kr' => '영혼 치유 기본 (KR 전용)'],
                'description' => ['kr' => '스트레스 유발 요인과 회복 방법을 알아보기 위한 간단한 평가입니다.'],
                'q' => [
                    ['kr' => '스트레스를 느낄 때 가장 먼저 어떤 반응을 보이나요?'],
                    ['kr' => '힘든 하루 후 가장 빨리 회복하는 데 도움이 되는 것은 무엇인가요?'],
                ],
                'a' => [
                    [
                        ['kr' => '쉽게 짜증이 납니다.'],
                        ['kr' => '말을 줄이고 혼자 있으려고 합니다.'],
                        ['kr' => '깊게 숨을 쉬고 집중합니다.'],
                    ],
                    [
                        ['kr' => '밖에서 산책하기.'],
                        ['kr' => '친구와 이야기하기.'],
                        ['kr' => '명상 또는 호흡 연습.'],
                    ],
                ],
            ],
            [
                'key' => 'A5_MIX',
                'title' => ['en' => 'Focus & Calm (EN+DE)', 'de' => 'Fokus & Ruhe (EN+DE)'],
                'description' => [
                    'en' => 'This scenario is partially translated to test missing locales (VI/KR).',
                    'de' => 'Dieses Szenario ist teilweise übersetzt, um fehlende Sprachen (VI/KR) zu testen.',
                ],
                'q' => [
                    ['en' => 'How often do you feel mentally overloaded?', 'de' => 'Wie oft fühlen Sie sich mental überlastet?'],
                    ['en' => 'What helps you regain focus?', 'de' => 'Was hilft Ihnen, den Fokus wiederzuerlangen?'],
                ],
                'a' => [
                    [
                        ['en' => 'Almost every day.', 'de' => 'Fast jeden Tag.'],
                        ['en' => 'A few times a week.', 'de' => 'Ein paar Mal pro Woche.'],
                        ['en' => 'Rarely.', 'de' => 'Selten.'],
                    ],
                    [
                        ['en' => 'Short breaks.', 'de' => 'Kurze Pausen.'],
                        ['en' => 'Planning the next step.', 'de' => 'Den nächsten Schritt planen.'],
                        ['en' => 'Breathing exercises.', 'de' => 'Atemübungen.'],
                    ],
                ],
            ],
        ];

        foreach ($scenarios as $scenario) {
            $assessmentId = DB::table('assessments')->insertGetId([
                'title' => json_encode($scenario['title'], JSON_UNESCAPED_UNICODE),
                'description' => json_encode($scenario['description'], JSON_UNESCAPED_UNICODE),
                'status' => 'created',
                'created_by' => $consultantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($scenario['q'] as $i => $qContent) {
                $questionId = DB::table('assessment_questions')->insertGetId([
                    'assessment_id' => $assessmentId,
                    'content' => json_encode($qContent, JSON_UNESCAPED_UNICODE),
                    'type' => 'single_choice',
                    'order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($scenario['a'][$i] as $j => $aContent) {
                    DB::table('assessment_options')->insert([
                        'question_id' => $questionId,
                        'content' => json_encode($aContent, JSON_UNESCAPED_UNICODE),
                        'score' => $j + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function seedDailyMissions(int $createdById): void
    {
        // Keep it idempotent: clear only missions created by our test consultant.
        if (DB::getDriverName() !== 'sqlite') {
            DB::table('daily_missions')->where('created_by_id', $createdById)->delete();
        } else {
            // SQLite in tests/dev commonly uses migrate:fresh; ok to wipe all.
            DB::table('daily_missions')->delete();
        }

        $missions = [
            [
                'title' => ['en' => '10-minute walk'],
                'description' => ['en' => 'Take a 10-minute walk and notice your breathing.'],
                'is_body' => true,
                'is_mind' => false,
                'is_wisdom' => false,
                'points' => 10,
            ],
            [
                'title' => ['kr' => '감사 일기 쓰기'],
                'description' => ['kr' => '오늘 감사한 일 3가지를 적어보세요.'],
                'is_wisdom' => true,
                'points' => 12,
            ],
            [
                'title' => ['en' => 'Deep breathing', 'vi' => 'Hít thở sâu', 'de' => 'Tiefes Atmen', 'kr' => '깊은 호흡'],
                'description' => [
                    'en' => 'Do 5 slow breaths: inhale 4s, exhale 6s.',
                    'vi' => 'Thực hiện 5 nhịp thở chậm: hít vào 4s, thở ra 6s.',
                    'de' => 'Machen Sie 5 langsame Atemzüge: 4s einatmen, 6s ausatmen.',
                    'kr' => '천천히 5번 호흡하세요: 4초 들이마시고 6초 내쉬기.',
                ],
                'is_mind' => true,
                'points' => 15,
            ],
            [
                'title' => ['vi' => 'Dọn dẹp góc làm việc'],
                'description' => ['vi' => 'Dọn dẹp bàn làm việc trong 5 phút để tạo cảm giác nhẹ nhàng.'],
                'is_mind' => true,
                'points' => 8,
            ],
            [
                'title' => ['de' => 'Kurze Dehnung'],
                'description' => ['de' => 'Dehnen Sie Rücken und Schultern für 3 Minuten.'],
                'is_body' => true,
                'points' => 10,
            ],
            [
                'title' => ['en' => 'No-phone break'],
                'description' => ['en' => 'Take a 15-minute break with no phone or social media.'],
                'is_mind' => true,
                'points' => 14,
            ],
            [
                'title' => ['kr' => '물 한 잔 마시기', 'en' => 'Drink a glass of water'],
                'description' => ['kr' => '천천히 물 한 잔을 마셔보세요.', 'en' => 'Drink one glass of water slowly.'],
                'is_body' => true,
                'points' => 6,
            ],
            [
                'title' => ['en' => 'Reflect on one lesson', 'de' => 'Über eine Lektion nachdenken', 'vi' => 'Suy ngẫm một bài học', 'kr' => '한 가지 교훈 돌아보기'],
                'description' => [
                    'en' => 'Write one lesson you learned today.',
                    'vi' => 'Viết ra một bài học bạn rút ra hôm nay.',
                    'de' => 'Schreiben Sie eine Lektion auf, die Sie heute gelernt haben.',
                    'kr' => '오늘 배운 교훈 한 가지를 적어보세요.',
                ],
                'is_wisdom' => true,
                'points' => 18,
            ],
        ];

        foreach ($missions as $m) {
            DailyMission::query()->create([
                'title' => $m['title'],
                'description' => $m['description'] ?? null,
                'points' => $m['points'] ?? 0,
                'is_body' => (bool) ($m['is_body'] ?? false),
                'is_mind' => (bool) ($m['is_mind'] ?? false),
                'is_wisdom' => (bool) ($m['is_wisdom'] ?? false),
                'created_by_id' => $createdById,
            ]);
        }
    }
}
