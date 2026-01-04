<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $sources = ['science', 'buddhism', 'christianity'];
        
        $videos = [
            // Body (4)
            [
                'url' => 'https://www.youtube.com/watch?v=inpok4MKVLM', // 5 Min Morning Yoga
                'category' => 'body',
                'title' => [
                    'en' => '5 Minute Morning Yoga',
                    'vi' => 'Yoga Buổi Sáng 5 Phút',
                    'de' => '5 Minuten Morgen-Yoga',
                    'kr' => '5분 모닝 요가'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=qULTwquOuT4', // 10 Min Stretch
                'category' => 'body',
                'title' => [
                    'en' => '10 Minute Full Body Stretch',
                    'vi' => 'Giãn Cơ Toàn Thân 10 Phút',
                    'de' => '10 Minuten Ganzkörperdehnung',
                    'kr' => '10분 전신 스트레칭'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=hJbRpHZr_d0', // Gentle Flow
                'category' => 'body',
                'title' => [
                    'en' => 'Gentle Yoga Flow',
                    'vi' => 'Yoga Nhẹ Nhàng',
                    'de' => 'Sanfter Yoga-Flow',
                    'kr' => '부드러운 요가 플로우'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=VecbXg40kXo', // Desk Yoga
                'category' => 'body',
                'title' => [
                    'en' => 'Yoga for Desk Workers',
                    'vi' => 'Yoga Cho Dân Văn Phòng',
                    'de' => 'Yoga für Büroarbeiter',
                    'kr' => '직장인을 위한 요가'
                ]
            ],
            
            // Mind (4)
            [
                'url' => 'https://www.youtube.com/watch?v=ZToicYcHIOU', // Mindfulness Meditation
                'category' => 'mind',
                'title' => [
                    'en' => 'Daily Mindfulness Meditation',
                    'vi' => 'Thiền Chánh Niệm Hàng Ngày',
                    'de' => 'Tägliche Achtsamkeitsmeditation',
                    'kr' => '매일 마음챙김 명상'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=syx3a1_LeFo', // Stress Relief
                'category' => 'mind',
                'title' => [
                    'en' => 'Meditation for Stress Relief',
                    'vi' => 'Thiền Giảm Căng Thẳng',
                    'de' => 'Meditation gegen Stress',
                    'kr' => '스트레스 해소 명상'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=O-6f5wQXSu8', // Sleep Music
                'category' => 'mind',
                'title' => [
                    'en' => 'Relaxing Sleep Music',
                    'vi' => 'Nhạc Ngủ Thư Giãn',
                    'de' => 'Entspannende Schlafmusik',
                    'kr' => '편안한 수면 음악'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=aaaaaa', // Anxiety Relief (Placeholder)
                'category' => 'mind',
                'title' => [
                    'en' => 'Quick Anxiety Relief',
                    'vi' => 'Giảm Lo Âu Nhanh Chóng',
                    'de' => 'Schnelle Linderung bei Angst',
                    'kr' => '빠른 불안 해소'
                ]
            ],

            // Wisdom (4)
            [
                'url' => 'https://www.youtube.com/watch?v=bbbbbb', // Stoicism
                'category' => 'wisdom',
                'title' => [
                    'en' => 'Introduction to Stoicism',
                    'vi' => 'Giới Thiệu Về Khắc Kỷ',
                    'de' => 'Einführung in den Stoizismus',
                    'kr' => '스토아 철학 입문'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=cccccc', // Buddhism
                'category' => 'wisdom',
                'title' => [
                    'en' => 'Basic Buddhist Teachings',
                    'vi' => 'Giáo Lý Phật Giáo Cơ Bản',
                    'de' => 'Grundlegende buddhistische Lehren',
                    'kr' => '기초 불교 가르침'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=dddddd', // Psychology
                'category' => 'wisdom',
                'title' => [
                    'en' => 'The Psychology of Happiness',
                    'vi' => 'Tâm Lý Học Về Hạnh Phúc',
                    'de' => 'Die Psychologie des Glücks',
                    'kr' => '행복의 심리학'
                ]
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=eeeeee', // Philosophy
                'category' => 'wisdom',
                'title' => [
                    'en' => 'Philosophy for Daily Life',
                    'vi' => 'Triết Học Cho Đời Sống',
                    'de' => 'Philosophie für den Alltag',
                    'kr' => '일상 생활을 위한 철학'
                ]
            ],
        ];

        foreach ($videos as $data) {
            Video::firstOrCreate(
                ['url' => $data['url']],
                [
                    'title' => $data['title'], // Translatable
                    'category' => $data['category'],
                    'language' => 'en', // Default language code, though title is translatable
                    'source_tag' => $sources[array_rand($sources)],
                    'is_active' => true,
                    'xp_reward' => 50,
                ]
            );
        }
    }
}
