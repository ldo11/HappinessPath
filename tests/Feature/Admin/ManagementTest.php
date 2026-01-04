<?php

namespace Tests\Feature\Admin;

use App\Models\DailyMission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_consultant_user_and_delete_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $create = $this->actingAs($admin)->post(route('user.admin.users.store', ['locale' => 'en']), [
            'name' => 'New Consultant',
            'email' => 'new_consultant@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'consultant',
            'city' => 'HCMC',
            'spiritual_preference' => 'buddhist',
            'geo_privacy' => 1,
        ]);
        $create->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'new_consultant@example.com',
            'role' => 'consultant',
        ]);

        $user = User::query()->where('email', 'new_consultant@example.com')->firstOrFail();

        $delete = $this->actingAs($admin)->delete(route('user.admin.users.destroy', ['locale' => 'en', 'user' => $user->id]));
        $delete->assertStatus(302);

        // Users are soft-deleted.
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_can_delete_daily_mission_created_by_consultant_override_power(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $consultant = User::factory()->create(['role' => 'consultant']);

        $mission = DailyMission::query()->create([
            'title' => ['en' => 'C mission'],
            'description' => ['en' => '...'],
            'points' => 5,
            'created_by_id' => $consultant->id,
        ]);

        $res = $this->actingAs($admin)->delete("/admin/daily-missions/{$mission->id}");
        $res->assertStatus(302);

        $this->assertDatabaseMissing('daily_missions', [
            'id' => $mission->id,
        ]);
    }

    public function test_admin_can_access_translator_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Dashboard is now at /en/translator/dashboard
        $res = $this->actingAs($admin)->get('/en/translator/dashboard');
        $res->assertOk();

        // UI Matrix
        $res = $this->actingAs($admin)->get('/en/translator/ui-matrix');
        $res->assertOk();
    }
}
