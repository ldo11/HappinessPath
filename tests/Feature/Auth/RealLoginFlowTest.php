<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_translator_login_redirect_and_content(): void
    {
        $this->seed(TestUsersSeeder::class);
        
        // 1. Create a Translator user explicitly to be sure
        $translator = User::where('email', 'translator@happiness.test')->first();
        if (!$translator) {
            $translator = User::factory()->create([
                'email' => 'translator@happiness.test',
                'role' => 'translator',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Perform a REAL login POST (no actingAs)
        $response = $this->post('/en/login', [
            'email' => 'translator@happiness.test',
            'password' => 'password',
        ]);

        // 3. Check Redirect
        // Expectation: Should go to /en/translator/dashboard
        $response->assertRedirect('/en/translator/dashboard');

        // 4. Follow Redirect and Check Content
        $response = $this->followingRedirects()->get('/en/translator/dashboard');
        
        $response->assertStatus(200);
        
        // 5. Check for unique content that proves we are on the CORRECT dashboard
        // The closure in web.php passes $languageLinesCount.
        // The Controller in translator.php might pass something else.
        // Let's check for specific text.
        $response->assertSee('Translator Dashboard'); // Generic
        
        // Check for a link that might be broken if routes are messed up
        // e.g., UI Matrix link
        $response->assertSee(route('translator.ui-matrix.index'));
    }
}
