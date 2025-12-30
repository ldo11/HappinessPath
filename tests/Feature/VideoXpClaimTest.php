<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserVideoLog;
use App\Models\Video;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoXpClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_claim_xp_once_per_video(): void
    {
        $this->seed(TestUsersSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        $video = Video::create([
            'title' => 'Test Video',
            'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'category' => 'mind',
            'language' => 'en',
            'pillar_tags' => ['mind'],
            'source_tags' => ['science'],
            'is_active' => true,
            'xp_reward' => 50,
        ]);

        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'Test Video',
        ]);

        $first = $this->actingAs($user)->postJson(route('videos.claim', ['locale' => 'en', 'videoId' => $video->id]));
        $this->assertSame(200, $first->getStatusCode(), $first->getContent());
        $first->assertJson([
            'claimed' => true,
            'xp_awarded' => 50,
        ]);

        $second = $this->actingAs($user)->postJson(route('videos.claim', ['locale' => 'en', 'videoId' => $video->id]));
        $this->assertSame(200, $second->getStatusCode(), $second->getContent());
        $second->assertJson([
            'claimed' => false,
            'xp_awarded' => 50,
        ]);

        $this->assertDatabaseCount('user_video_logs', 1);

        $log = UserVideoLog::where('user_id', $user->id)->where('video_id', $video->id)->firstOrFail();
        $this->assertNotNull($log->claimed_at);
        $this->assertSame(50, (int) $log->xp_awarded);
    }
}
