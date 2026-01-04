<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_standard_fields()
    {
        $user = User::factory()->create([
            'xp_body' => 0,
            'xp_mind' => 0,
            'xp_wisdom' => 0,
        ]);

        $response = $this->actingAs($user)
            ->post(route('user.profile.settings.update', ['locale' => 'en']), [
                'name' => 'New Name',
                'nickname' => 'CoolNick',
                'introduction' => 'Hello world',
                'display_language' => 'vi',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'nickname' => 'CoolNick',
            'introduction' => 'Hello world',
            'display_language' => 'vi',
        ]);

        $user->refresh();
        $this->assertEquals(0, $user->xp_body);
        $this->assertEquals(0, $user->xp_mind);
        $this->assertEquals(0, $user->xp_wisdom);
    }

    public function test_user_cannot_update_xp_fields()
    {
        $user = User::factory()->create([
            'xp_body' => 0,
            'xp_mind' => 0,
            'xp_wisdom' => 0,
        ]);

        $response = $this->actingAs($user)
            ->post(route('user.profile.settings.update', ['locale' => 'en']), [
                'name' => 'New Name',
                'xp_wisdom' => 1000,
                'xp_body' => 500,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);

        $user->refresh();
        $this->assertEquals(0, $user->xp_wisdom);
        $this->assertEquals(0, $user->xp_body);
    }
}
