<?php

namespace Tests\Feature\AccessControl;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_panels(): void
    {
        $admin = User::factory()->admin()->create();

        // Admin can access admin panel
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Admin Dashboard');

        // Admin can access consultant panel
        $response = $this->actingAs($admin)->get('/en/consultant/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Consultant Dashboard');

        // Admin can access translator panel
        $response = $this->actingAs($admin)->get('/en/translator/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Translator Portal');
    }

    public function test_consultant_can_access_consultant_panel(): void
    {
        $consultant = User::factory()->consultant()->create();

        $response = $this->actingAs($consultant)->get('/en/consultant/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Consultant Dashboard');
    }

    public function test_consultant_cannot_access_admin_panel(): void
    {
        $consultant = User::factory()->consultant()->create();

        $response = $this->actingAs($consultant)->get('/admin/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_consultant_cannot_access_translator_panel(): void
    {
        $consultant = User::factory()->consultant()->create();

        $response = $this->actingAs($consultant)->get('/en/translator/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_translator_can_access_translator_panel(): void
    {
        $translator = User::factory()->translator()->create();

        $response = $this->actingAs($translator)->get('/en/translator/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Translator Portal');
    }

    public function test_translator_cannot_access_admin_panel(): void
    {
        $translator = User::factory()->translator()->create();

        $response = $this->actingAs($translator)->get('/admin/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_translator_cannot_access_consultant_panel(): void
    {
        $translator = User::factory()->translator()->create();

        $response = $this->actingAs($translator)->get('/en/consultant/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_user_can_access_user_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/en/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Dashboard');
    }

    public function test_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_user_cannot_access_consultant_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/en/consultant/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_user_cannot_access_translator_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/en/translator/dashboard');
        $response->assertForbidden(); // 403
    }

    public function test_guest_cannot_access_any_panel(): void
    {
        // Guest cannot access admin panel
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login?locale=en');

        // Guest cannot access consultant panel
        $response = $this->get('/en/consultant/dashboard');
        $response->assertRedirect('/login?locale=en');

        // Guest cannot access translator panel
        $response = $this->get('/en/translator/dashboard');
        $response->assertRedirect('/login?locale=en');

        // Guest cannot access user dashboard
        $response = $this->get('/en/dashboard');
        $response->assertRedirect('/login?locale=en');
    }

    public function test_admin_can_access_admin_management_pages(): void
    {
        $admin = User::factory()->admin()->create();

        // Test access to various admin pages
        $adminRoutes = [
            '/admin/users',
            '/admin/languages',
            '/admin/videos',
            '/admin/daily-missions',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_consultant_cannot_access_admin_management_pages(): void
    {
        $consultant = User::factory()->consultant()->create();

        $adminRoutes = [
            '/admin/users',
            '/admin/languages',
            '/admin/videos',
            '/admin/daily-missions',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($consultant)->get($route);
            $response->assertForbidden();
        }
    }

    public function test_translator_cannot_access_admin_management_pages(): void
    {
        $translator = User::factory()->translator()->create();

        $adminRoutes = [
            '/admin/users',
            '/admin/languages',
            '/admin/videos',
            '/admin/daily-missions',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($translator)->get($route);
            $response->assertForbidden();
        }
    }

    public function test_translator_can_access_language_lines(): void
    {
        $translator = User::factory()->translator()->create();

        $response = $this->actingAs($translator)
            ->get('/en/translator/language-lines');

        $response->assertStatus(200);
        $response->assertSeeText('Translation Matrix');
        $response->assertSeeText('Key | VI | EN | DE | KR');
    }
}
