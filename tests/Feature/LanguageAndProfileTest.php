<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageAndProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_page_displays_user_fields()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'introduction' => 'This is my intro.',
            'location' => 'Hanoi, Vietnam',
            'display_language' => 'en',
        ]);

        $response = $this->actingAs($user)
            ->get(route('user.profile.settings.edit', ['locale' => 'en']));

        $response->assertStatus(200);
        $response->assertSee('This is my intro.');
        $response->assertSee('Hanoi, Vietnam');
        $response->assertSee('English'); 
    }

    /** @test */
    public function user_sees_content_in_their_display_language()
    {
        // 1. Vietnamese User
        $userVi = User::factory()->create(['display_language' => 'vi']);
        $response = $this->actingAs($userVi)->get('/'); 
        $response->assertRedirect('/vi/login'); 
        
        $response = $this->actingAs($userVi)->get('/vi/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Bảng điều khiển'); // From vi.json "title"

        // 2. German User
        $userDe = User::factory()->create(['display_language' => 'de']);
        $response = $this->actingAs($userDe)->get('/');
        $response->assertRedirect('/de/login');

        $response = $this->actingAs($userDe)->get('/de/dashboard');
        $response->assertStatus(200);
        // We will assert seeing a German string. We might need to add one if it doesn't exist.
        // For now, let's assume we expect "Armaturenbrett" or similar, or checks the html lang.
        $response->assertSee('lang="de"', false); 

        // 3. Korean User
        $userKr = User::factory()->create(['display_language' => 'kr']);
        $response = $this->actingAs($userKr)->get('/');
        $response->assertRedirect('/kr/login');

        $response = $this->actingAs($userKr)->get('/kr/dashboard');
        $response->assertStatus(200);
        $response->assertSee('lang="kr"', false);

        // 4. English User
        $userEn = User::factory()->create(['display_language' => 'en']);
        $response = $this->actingAs($userEn)->get('/');
        $response->assertRedirect('/en/login');

        $response = $this->actingAs($userEn)->get('/en/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function guest_falls_back_to_english_default()
    {
        // No session, no user
        $response = $this->get('/');
        
        // Should redirect to /en/login (assuming app.locale is 'en')
        $response->assertRedirect('/en/login');
        
        $response = $this->followRedirects($response);
        $response->assertSee('lang="en"', false);
    }
    
    /** @test */
    public function geo_locale_api_returns_correct_locale()
    {
        // Test Vietnam Coordinates
        $response = $this->postJson(route('api.detect-locale'), [
            'latitude' => 21.0285,
            'longitude' => 105.8542
        ]);
        $response->assertOk()->assertJson(['locale' => 'vi']);

        // Test Germany Coordinates
        $response = $this->postJson(route('api.detect-locale'), [
            'latitude' => 52.5200,
            'longitude' => 13.4050
        ]);
        $response->assertOk()->assertJson(['locale' => 'de']);
        
        // Test Korea Coordinates
        $response = $this->postJson(route('api.detect-locale'), [
            'latitude' => 37.5665,
            'longitude' => 126.9780
        ]);
        $response->assertOk()->assertJson(['locale' => 'kr']);
        
        // Test Fallback (e.g. USA)
        $response = $this->postJson(route('api.detect-locale'), [
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ]);
        $response->assertOk()->assertJson(['locale' => 'en']);
    }
}
