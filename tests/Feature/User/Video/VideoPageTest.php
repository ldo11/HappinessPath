<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'language' => 'en',
            'religion' => 'christianity'
        ]);
    }

    /** @test */
    public function user_sees_videos_matching_their_language_and_religion()
    {
        // Create English/Christian video
        $englishChristianVideo = Video::factory()->create([
            'title' => 'English Christian Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=test1'
        ]);

        // Create Vietnamese/Buddhist video
        $vietnameseBuddhistVideo = Video::factory()->create([
            'title' => 'Vietnamese Buddhist Video',
            'language' => 'vi',
            'source_tags' => ['buddhism'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=test2'
        ]);

        // Create another English video with different religion (but still accessible)
        $englishScienceVideo = Video::factory()->create([
            'title' => 'English Science Video',
            'language' => 'en',
            'source_tags' => ['science'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=test3'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/en/videos');

        $response->assertStatus(200);
        $response->assertSee('English Christian Video');
        $response->assertSee('English Science Video'); // Same language, different religion
        $response->assertDontSee('Vietnamese Buddhist Video'); // Different language
    }

    /** @test */
    public function user_only_sees_active_videos()
    {
        // Create active English video
        $activeVideo = Video::factory()->create([
            'title' => 'Active English Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=active'
        ]);

        // Create inactive English video
        $inactiveVideo = Video::factory()->create([
            'title' => 'Inactive English Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => false,
            'url' => 'https://www.youtube.com/watch?v=inactive'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/en/videos');

        $response->assertStatus(200);
        $response->assertSee('Active English Video');
        $response->assertDontSee('Inactive English Video');
    }

    /** @test */
    public function user_with_vietnamese_language_sees_vietnamese_videos()
    {
        // Create Vietnamese user
        $vietnameseUser = User::factory()->create([
            'language' => 'vi',
            'religion' => 'buddhism'
        ]);

        // Create Vietnamese/Buddhist video
        $vietnameseBuddhistVideo = Video::factory()->create([
            'title' => 'Video Tiếng Việt Phật Giáo',
            'language' => 'vi',
            'source_tags' => ['buddhism'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=vietnamese'
        ]);

        // Create English/Christian video
        $englishChristianVideo = Video::factory()->create([
            'title' => 'English Christian Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=english'
        ]);

        $response = $this->actingAs($vietnameseUser)
            ->get('/vi/videos');

        $response->assertStatus(200);
        $response->assertSee('Video Tiếng Việt Phật Giáo');
        $response->assertDontSee('English Christian Video');
    }

    /** @test */
    public function user_sees_videos_filtered_by_pillar_tags()
    {
        // Create a video with body pillar
        $bodyVideo = Video::factory()->create([
            'title' => 'Body Video Only',
            'language' => 'en',
            'pillar_tags' => ['body'],
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=body'
        ]);

        // Create a video with mind pillar
        $mindVideo = Video::factory()->create([
            'title' => 'Mind Video Only',
            'language' => 'en',
            'pillar_tags' => ['mind'],
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=mind'
        ]);

        // Test filtering by body pillar - should only see body video
        $response = $this->actingAs($this->user)
            ->get('/en/videos?pillar=body');

        $response->assertStatus(200);
        $response->assertSee('Body Video Only');
        $response->assertDontSee('Mind Video Only');

        // Test filtering by mind pillar - should only see mind video
        $response = $this->actingAs($this->user)
            ->get('/en/videos?pillar=mind');

        $response->assertStatus(200);
        $response->assertSee('Mind Video Only');
        $response->assertDontSee('Body Video Only');
    }

    //    /** @test */
//    public function guest_can_view_videos_page()
//    {
//        // Create some videos
//        Video::factory()->count(3)->create([
//            'language' => 'en',
//            'is_active' => true
//        ]);
//
//        $response = $this->get('/en/videos');
//
//        $response->assertStatus(200);
//        $response->assertViewIs('videos.index');
//    }

    /** @test */
    public function video_page_shows_thumbnails_and_titles()
    {
        $video = Video::factory()->create([
            'title' => 'Test Video with Embed',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/en/videos');

        $response->assertStatus(200);
        $response->assertSee('Test Video with Embed');
        $response->assertSee('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg');
    }

    /** @test */
    public function user_can_view_individual_video_page()
    {
        $video = Video::factory()->create([
            'title' => 'Individual Test Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=single'
        ]);

        $response = $this->actingAs($this->user)
            ->get("/en/videos/{$video->id}");

        $response->assertStatus(200);
        $response->assertSee('Individual Test Video');
        $response->assertViewIs('videos.show');
    }

    /** @test */
    public function user_cannot_view_inactive_individual_video()
    {
        $inactiveVideo = Video::factory()->create([
            'title' => 'Inactive Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => false,
            'url' => 'https://www.youtube.com/watch?v=inactive'
        ]);

        $response = $this->actingAs($this->user)
            ->get("/en/videos/{$inactiveVideo->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_claim_xp_for_watching_video()
    {
        $video = Video::factory()->create([
            'title' => 'XP Reward Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'xp_reward' => 10,
            'url' => 'https://www.youtube.com/watch?v=xp'
        ]);

        $response = $this->actingAs($this->user)
            ->post("/en/videos/{$video->id}/claim");

        $response->assertStatus(200)
                 ->assertJson([
                     'claimed' => true,
                     'xp_awarded' => 10
                 ]);

        // Assert user video log is created
        $this->assertDatabaseHas('user_video_logs', [
            'user_id' => $this->user->id,
            'video_id' => $video->id,
            'xp_awarded' => 10,
        ]);
    }

    /** @test */
    public function user_cannot_claim_xp_twice_for_same_video()
    {
        $video = Video::factory()->create([
            'title' => 'Double XP Video',
            'language' => 'en',
            'source_tags' => ['christianity'],
            'is_active' => true,
            'xp_reward' => 10,
            'url' => 'https://www.youtube.com/watch?v=double'
        ]);

        // Claim XP first time
        $response = $this->actingAs($this->user)
            ->post("/en/videos/{$video->id}/claim");

        $response->assertStatus(200)
                 ->assertJson([
                     'claimed' => true,
                     'xp_awarded' => 10
                 ]);

        // Try to claim XP second time
        $response = $this->actingAs($this->user)
            ->post("/en/videos/{$video->id}/claim");

        $response->assertStatus(200)
                 ->assertJson([
                     'claimed' => false,
                     'xp_awarded' => 10
                 ]);

        // Assert only one record exists
        $this->assertEquals(1, $this->user->userVideoLogs()->where('video_id', $video->id)->count());
    }

    /** @test */
    public function video_filtering_works_with_multiple_parameters()
    {
        // Create videos with various combinations
        $perfectMatch = Video::factory()->create([
            'title' => 'Perfect Match Video',
            'language' => 'en',
            'pillar_tags' => ['heart'],
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=perfect'
        ]);

        $partialMatch = Video::factory()->create([
            'title' => 'Partial Match Video',
            'language' => 'en',
            'pillar_tags' => ['mind'], // Different pillar
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=partial'
        ]);

        $differentLanguage = Video::factory()->create([
            'title' => 'Different Language Video',
            'language' => 'vi', // Different language
            'pillar_tags' => ['heart'],
            'source_tags' => ['christianity'],
            'is_active' => true,
            'url' => 'https://www.youtube.com/watch?v=different'
        ]);

        // Filter with multiple criteria
        $response = $this->actingAs($this->user)
            ->get('/en/videos?pillar_tags[]=heart&source_tags[]=christianity');

        $response->assertStatus(200);
        $response->assertSee('Perfect Match Video');
        $response->assertSee('Partial Match Video'); // Matches language and source
        $response->assertDontSee('Different Language Video'); // Different language
    }
}
