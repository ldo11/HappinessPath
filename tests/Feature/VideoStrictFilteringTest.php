<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoStrictFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_strict_language_and_tag_filtering(): void
    {
        $user = User::factory()->create([
            'language' => 'en',
            'religion' => 'christianity',
            'email_verified_at' => now(),
        ]);

        Video::create([
            'title' => 'VI Buddhism',
            'url' => 'https://www.youtube.com/watch?v=nO3_dytV0GY',
            'category' => 'mind',
            'language' => 'vi',
            'pillar_tags' => ['body', 'mind'],
            'source_tags' => ['buddhism'],
            'is_active' => true,
        ]);

        Video::create([
            'title' => 'EN Christianity',
            'url' => 'https://www.youtube.com/watch?v=kHQ-gOQO144',
            'category' => 'mind',
            'language' => 'en',
            'pillar_tags' => ['body', 'mind'],
            'source_tags' => ['christianity'],
            'is_active' => true,
        ]);

        Video::create([
            'title' => 'EN Science',
            'url' => 'https://www.youtube.com/watch?v=Aw71zanwMnY',
            'category' => 'mind',
            'language' => 'en',
            'pillar_tags' => ['mind', 'wisdom'],
            'source_tags' => ['science'],
            'is_active' => true,
        ]);

        Video::create([
            'title' => 'EN Buddhism',
            'url' => 'https://www.youtube.com/watch?v=KxGRhd_iWuE',
            'category' => 'mind',
            'language' => 'en',
            'pillar_tags' => ['mind'],
            'source_tags' => ['buddhism'],
            'is_active' => true,
        ]);

        $res = $this->actingAs($user)->get('/en/videos?source=christianity');
        $res->assertOk();

        $res->assertSee('EN Christianity');
        $res->assertDontSee('EN Science');
        $res->assertDontSee('EN Buddhism');
        $res->assertDontSee('VI Buddhism');
    }

    public function test_video_show_is_blocked_when_not_allowed_by_profile(): void
    {
        $user = User::factory()->create([
            'language' => 'en',
            'religion' => 'science',
            'email_verified_at' => now(),
        ]);

        $video = Video::create([
            'title' => 'EN Buddhism',
            'url' => 'https://www.youtube.com/watch?v=KxGRhd_iWuE',
            'category' => 'mind',
            'language' => 'en',
            'pillar_tags' => ['mind'],
            'source_tags' => ['buddhism'],
            'is_active' => true,
        ]);

        $res = $this->actingAs($user)->get(route('videos.show', ['locale' => 'en', 'videoId' => $video->id]));
        $res->assertNotFound();
    }
}
