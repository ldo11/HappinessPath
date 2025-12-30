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

        $response = $this->actingAs($user)->followingRedirects()->get('/en/dashboard');
        $response->assertOk();
        $response->assertSessionHas('locale', 'de');
    }
}
