<?php

namespace Database\Seeders;

use App\Models\DailyMission;
use App\Models\MissionSet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DailyMissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();

        // Create Mission Set (Idempotent)
        $missionSet = MissionSet::firstOrCreate(
            ['name->en' => '30 Days of Mindfulness'],
            [
                'name' => [
                    'en' => '30 Days of Mindfulness',
                    'vi' => '30 Ngày Chánh Niệm',
                    'de' => '30 Tage der Achtsamkeit',
                    'kr' => '마음챙김의 30일',
                ],
                'description' => [
                    'en' => 'A journey to cultivate peace and clarity.',
                    'vi' => 'Hành trình nuôi dưỡng sự bình an và sáng suốt.',
                    'de' => 'Eine Reise, um Frieden und Klarheit zu kultivieren.',
                    'kr' => '평화와 명확성을 기르기 위한 여정.',
                ],
                'type' => 'mindfulness',
                'created_by' => $admin->id ?? 1,
                'is_default' => true,
            ]
        );

        $missions = [
            1 => [
                'title' => ['en' => 'Focus on Breath', 'vi' => 'Tập trung hơi thở', 'de' => 'Fokus auf den Atem', 'kr' => '호흡에 집중하기'],
                'desc' => ['en' => 'Spend 5 minutes focusing solely on your breath.', 'vi' => 'Dành 5 phút chỉ tập trung vào hơi thở của bạn.', 'de' => 'Verbringen Sie 5 Minuten damit, sich ausschließlich auf Ihren Atem zu konzentrieren.', 'kr' => '오직 호흡에만 집중하며 5분을 보내세요.']
            ],
            2 => [
                'title' => ['en' => 'Body Scan', 'vi' => 'Quét cơ thể', 'de' => 'Körperscan', 'kr' => '바디 스캔'],
                'desc' => ['en' => 'Notice sensations in your body from toe to head.', 'vi' => 'Chú ý cảm giác trong cơ thể từ ngón chân đến đầu.', 'de' => 'Achten Sie auf Empfindungen in Ihrem Körper von den Zehen bis zum Kopf.', 'kr' => '발끝부터 머리까지 신체의 감각을 알아차리세요.']
            ],
            3 => [
                'title' => ['en' => 'Mindful Walking', 'vi' => 'Đi bộ chánh niệm', 'de' => 'Achtsames Gehen', 'kr' => '마음챙김 걷기'],
                'desc' => ['en' => 'Walk slowly and feel each step.', 'vi' => 'Đi chậm và cảm nhận từng bước chân.', 'de' => 'Gehen Sie langsam und fühlen Sie jeden Schritt.', 'kr' => '천천히 걸으며 각 발걸음을 느끼세요.']
            ],
            4 => [
                'title' => ['en' => 'Gratitude Journal', 'vi' => 'Nhật ký biết ơn', 'de' => 'Dankbarkeitstagebuch', 'kr' => '감사 일기'],
                'desc' => ['en' => 'Write down 3 things you are grateful for.', 'vi' => 'Viết ra 3 điều bạn biết ơn.', 'de' => 'Schreiben Sie 3 Dinge auf, für die Sie dankbar sind.', 'kr' => '감사한 일 3가지를 적어보세요.']
            ],
            5 => [
                'title' => ['en' => 'Mindful Eating', 'vi' => 'Ăn chánh niệm', 'de' => 'Achtsames Essen', 'kr' => '마음챙김 식사'],
                'desc' => ['en' => 'Eat a meal without distractions.', 'vi' => 'Ăn một bữa không xao nhãng.', 'de' => 'Essen Sie eine Mahlzeit ohne Ablenkungen.', 'kr' => '주의를 산만하게 하는 것 없이 식사를 하세요.']
            ],
            // ... generating pattern for 30 days
        ];

        // Fill up to 30 days with a loop pattern if not fully defined
        for ($day = 1; $day <= 30; $day++) {
            $data = $missions[$day] ?? [
                'title' => [
                    'en' => "Day $day Practice",
                    'vi' => "Thực hành Ngày $day",
                    'de' => "Tag $day Übung",
                    'kr' => "$day 일차 실천",
                ],
                'desc' => [
                    'en' => "Continue your mindfulness journey.",
                    'vi' => "Tiếp tục hành trình chánh niệm của bạn.",
                    'de' => "Setzen Sie Ihre Achtsamkeitsreise fort.",
                    'kr' => "마음챙김 여정을 계속하세요.",
                ]
            ];

            // Random tags
            $isBody = rand(0, 1) == 1;
            $isMind = rand(0, 1) == 1;
            $isWisdom = (!$isBody && !$isMind) ? true : (rand(0, 1) == 1);

            DailyMission::create([
                'mission_set_id' => $missionSet->id,
                'day_number' => $day,
                'title' => $data['title'],
                'description' => $data['desc'],
                'points' => 50,
                'is_body' => $isBody,
                'is_mind' => $isMind,
                'is_wisdom' => $isWisdom,
                'created_by_id' => $admin->id,
            ]);
        }
        
        // Task 3: Assign to Test Users
        $testEmails = [
            'user_en@happiness.test',
            'user_vi@happiness.test',
            'user_de@happiness.test',
            'user_kr@happiness.test'
        ];

        $users = User::whereIn('email', $testEmails)->get();
        foreach ($users as $user) {
            $user->update([
                'active_mission_set_id' => $missionSet->id,
                'mission_started_at' => now(),
            ]);
        }
    }
}
