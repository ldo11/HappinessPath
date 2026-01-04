<?php

namespace Tests\Feature\Consultant;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_view_video_list(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        $video = Video::factory()->create();

        $response = $this->actingAs($consultant)->get(route('consultant.videos.index', ['locale' => 'en']));

        $response->assertStatus(200);
        $response->assertSee($video->title);
    }

    public function test_consultant_can_access_create_video_page(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);

        $response = $this->actingAs($consultant)->get(route('consultant.videos.create', ['locale' => 'en']));

        $response->assertStatus(200);
    }

    public function test_consultant_can_create_video(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        
        $data = [
            'title' => 'New Video',
            'description' => 'Video Description',
            'url' => 'https://youtube.com/watch?v=12345678',
            'thumbnail_url' => 'https://img.youtube.com/vi/12345678/0.jpg',
            'duration' => 120,
            'language' => 'en',
            'is_active' => true,
        ];

        $response = $this->actingAs($consultant)->post(route('consultant.videos.store', ['locale' => 'en']), $data);

        if (session('errors')) {
            dump(session('errors')->all());
        }

        $response->assertRedirect(route('consultant.videos.index', ['locale' => 'en']));
        
        $video = Video::where('url', 'https://youtube.com/watch?v=12345678')->first();
        $this->assertNotNull($video);
        $this->assertEquals('New Video', $video->title);
    }

    public function test_consultant_can_access_edit_video_page(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        $video = Video::factory()->create();

        $response = $this->actingAs($consultant)->get(route('consultant.videos.edit', ['locale' => 'en', 'video' => $video->id]));

        $response->assertStatus(200);
        $response->assertSee($video->title);
    }

    public function test_consultant_can_update_video(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        $video = Video::factory()->create();

        $data = [
            'title' => 'Updated Video',
            'description' => 'Updated Description',
            'url' => $video->url,
            'thumbnail_url' => $video->thumbnail_url,
            'duration' => 150,
            'language' => 'en',
            'is_active' => true,
        ];

        $response = $this->actingAs($consultant)->put(route('consultant.videos.update', ['locale' => 'en', 'video' => $video->id]), $data);

        $response->assertRedirect(route('consultant.videos.edit', ['locale' => 'en', 'video' => $video->id]));
        
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title->en' => 'Updated Video',
        ]);
        
        $video->refresh();
        $this->assertEquals('Updated Video', $video->title);
    }

    public function test_consultant_can_delete_video(): void
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        $video = Video::factory()->create();

        $response = $this->actingAs($consultant)->delete(route('consultant.videos.destroy', ['locale' => 'en', 'video' => $video->id]));

        $response->assertRedirect(route('consultant.videos.index', ['locale' => 'en']));
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }
}
