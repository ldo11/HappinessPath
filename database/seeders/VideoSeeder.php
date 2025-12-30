<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            [
                'title' => 'Thiền Buông Thư (Thầy Minh Niệm)',
                'url' => 'https://www.youtube.com/watch?v=nO3_dytV0GY',
                'language' => 'vi',
                'pillar_tags' => ['body', 'mind'],
                'source_tags' => ['buddhism'],
            ],
            [
                'title' => 'Giấc ngủ dưới góc nhìn Khoa học',
                'url' => 'https://www.youtube.com/watch?v=5MuIMqhT8DM',
                'language' => 'vi',
                'pillar_tags' => ['body'],
                'source_tags' => ['science'],
            ],
            [
                'title' => 'Christian Meditation for Sleep',
                'url' => 'https://www.youtube.com/watch?v=kHQ-gOQO144',
                'language' => 'en',
                'pillar_tags' => ['body', 'mind'],
                'source_tags' => ['christianity'],
            ],
            [
                'title' => 'The Science of Mindfulness (TED)',
                'url' => 'https://www.youtube.com/watch?v=Aw71zanwMnY',
                'language' => 'en',
                'pillar_tags' => ['mind', 'wisdom'],
                'source_tags' => ['science'],
            ],
            [
                'title' => 'Achtsamkeit im Alltag',
                'url' => 'https://www.youtube.com/watch?v=xyz123',
                'language' => 'de',
                'pillar_tags' => ['mind'],
                'source_tags' => ['science'],
            ],
        ];

        foreach ($videos as $v) {
            $category = $v['pillar_tags'][0] ?? 'mind';
            Video::query()->updateOrCreate(
                ['title' => $v['title']],
                [
                    'title' => $v['title'],
                    'url' => $v['url'],
                    'language' => $v['language'],
                    'pillar_tags' => $v['pillar_tags'],
                    'source_tags' => $v['source_tags'],
                    'pillar_tag' => $v['pillar_tags'][0] ?? null,
                    'source_tag' => $v['source_tags'][0] ?? null,
                    'category' => $category,
                    'is_active' => true,
                ]
            );
        }
    }
}
