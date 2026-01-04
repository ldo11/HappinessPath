<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_redirected_to_preferred_language_dashboard_after_login()
    {
        // 1. Create User KR (display_language = 'kr')
        $userKr = User::factory()->create([
            'email' => 'korean@example.com',
            'password' => bcrypt('password'),
            'display_language' => 'kr',
            'role' => 'user',
        ]);

        // 2. Post to /en/login (Login from English page)
        $response = $this->post('/en/login', [
            'email' => 'korean@example.com',
            'password' => 'password',
        ]);

        // 3. AssertRedirect: To /kr/dashboard
        $response->assertRedirect('/kr/dashboard');
        
        // Follow Redirect -> Assert session locale is 'kr'
        $this->followRedirects($response)->assertSessionHas('locale', 'kr');
    }

    public function test_user_vi_is_redirected_to_vi_dashboard()
    {
        $userVi = User::factory()->create([
            'email' => 'vietnamese@example.com',
            'password' => bcrypt('password'),
            'display_language' => 'vi',
            'role' => 'user',
        ]);

        $response = $this->post('/en/login', [
            'email' => 'vietnamese@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/vi/dashboard');
    }

    public function test_fallback_route_redirects_to_preferred_locale()
    {
        $userDe = User::factory()->create([
            'email' => 'german@example.com',
            'display_language' => 'de',
            'role' => 'user',
        ]);

        // Acting as user, visit a non-localized path that should be caught by fallback or root redirect
        // Visits /dashboard (no locale)
        $response = $this->actingAs($userDe)->get('/dashboard');
        
        // Should redirect to /de/dashboard
        $response->assertRedirect('/de/dashboard');
    }
}
