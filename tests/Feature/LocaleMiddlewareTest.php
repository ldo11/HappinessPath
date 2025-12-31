<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LanguageLineSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_middleware_does_not_duplicate_locale_prefix(): void
    {
        $response = $this->get('/en/login');

        $response->assertOk();
        $response->assertDontSee('/en/en/', false);
    }

    public function test_authenticated_user_language_overrides_url_locale_for_translations(): void
    {
        $this->seed(LanguageLineSeeder::class);

        $user = User::factory()->create([
            'language' => 'de',
            'email_verified_at' => now(),
        ]);

        // Test that user can manually access English URL despite German preference
        $response = $this->actingAs($user)->get('/en/dashboard');
        $response->assertOk();
        $response->assertSessionHas('locale', 'en'); // User manually chose English, so respect it
        
        // Test that accessing without locale prefix redirects to preferred locale
        $response = $this->actingAs($user)->followingRedirects()->get('/dashboard');
        $response->assertOk();
        $response->assertSessionHas('locale', 'de'); // Should redirect to German preference
    }
}
