<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_converts_youtube_watch_url_to_embed_url()
    {
        $video = Video::factory()->create([
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);

        $expected = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals($expected, $video->embed_url);
    }

    /** @test */
    public function it_converts_youtube_short_url_to_embed_url()
    {
        $video = Video::factory()->create([
            'url' => 'https://youtu.be/dQw4w9WgXcQ'
        ]);

        $expected = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals($expected, $video->embed_url);
    }

    /** @test */
    public function it_converts_youtube_embed_url_to_embed_url()
    {
        $video = Video::factory()->create([
            'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'
        ]);

        $expected = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals($expected, $video->embed_url);
    }

    /** @test */
    public function it_returns_original_url_if_not_youtube()
    {
        $originalUrl = 'https://vimeo.com/123456789';
        $video = Video::factory()->create([
            'url' => $originalUrl
        ]);

        $this->assertEquals($originalUrl, $video->embed_url);
    }

    /** @test */
    public function it_returns_null_for_empty_url()
    {
        $video = Video::factory()->create(['url' => '']);
        $this->assertNull($video->embed_url);
    }

    /** @test */
    public function it_filters_by_pillar_tags_json_column()
    {
        Video::factory()->create([
            'pillar_tags' => ['body', 'wisdom'],
            'language' => 'en'
        ]);

        Video::factory()->create([
            'pillar_tags' => ['mind', 'body'],
            'language' => 'en'
        ]);

        Video::factory()->create([
            'pillar_tags' => ['body', 'heart'],
            'language' => 'vi'
        ]);

        // Test filtering by single pillar tag
        $filtered = Video::filter(['pillar_tags' => ['body']])->get();
        $this->assertCount(3, $filtered);
        $this->assertTrue($filtered->every(fn($v) => in_array('body', $v->pillar_tags)));

        // Test filtering by multiple pillar tags (returns videos containing ANY of the tags)
        $filtered = Video::filter(['pillar_tags' => ['body', 'mind']])->get();
        $this->assertCount(3, $filtered); // All 3 videos have 'body' or 'mind'
    }

    /** @test */
    public function it_filters_by_source_tags_json_column()
    {
        Video::factory()->create([
            'source_tags' => ['christianity', 'buddhism'],
            'language' => 'en'
        ]);

        Video::factory()->create([
            'source_tags' => ['buddhism', 'science'],
            'language' => 'en'
        ]);

        Video::factory()->create([
            'source_tags' => ['christianity', 'science'],
            'language' => 'vi'
        ]);

        // Test filtering by single source tag
        $filtered = Video::filter(['source_tags' => ['christianity']])->get();
        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($v) => in_array('christianity', $v->source_tags)));

        // Test filtering by multiple source tags (returns videos containing ANY of the tags)
        $filtered = Video::filter(['source_tags' => ['buddhism', 'science']])->get();
        $this->assertCount(3, $filtered); // All 3 videos have 'buddhism' or 'science' or 'christianity' (but we're filtering for buddhism OR science)
    }

    /** @test */
    public function it_filters_by_language()
    {
        Video::factory()->count(3)->create(['language' => 'en']);
        Video::factory()->count(2)->create(['language' => 'vi']);
        Video::factory()->count(1)->create(['language' => 'de']);

        $filtered = Video::filter(['language' => 'en'])->get();
        $this->assertCount(3, $filtered);
        $this->assertTrue($filtered->every(fn($v) => $v->language === 'en'));
    }

    /** @test */
    public function it_filters_by_multiple_criteria()
    {
        Video::factory()->create([
            'pillar_tags' => ['heart', 'wisdom'],
            'source_tags' => ['christianity'],
            'language' => 'en',
            'is_active' => true
        ]);

        Video::factory()->create([
            'pillar_tags' => ['heart'],
            'source_tags' => ['buddhism'],
            'language' => 'vi',
            'is_active' => true
        ]);

        Video::factory()->create([
            'pillar_tags' => ['heart', 'wisdom'],
            'source_tags' => ['christianity'],
            'language' => 'en',
            'is_active' => false
        ]);

        $filtered = Video::filter([
            'pillar_tags' => ['heart'],
            'source_tags' => ['christianity'],
            'language' => 'en',
            'is_active' => true
        ])->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals('en', $filtered->first()->language);
        $this->assertTrue(in_array('heart', $filtered->first()->pillar_tags));
        $this->assertTrue(in_array('christianity', $filtered->first()->source_tags));
    }

    /** @test */
    public function it_returns_all_records_when_no_filters_applied()
    {
        Video::factory()->count(5)->create();

        $filtered = Video::filter([])->get();
        $this->assertCount(5, $filtered);
    }
}
