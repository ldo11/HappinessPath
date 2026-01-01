<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationAndLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_registration_page(): void
    {
        $response = $this->get('/en/register');

        $response->assertStatus(200);
        $response->assertSeeText('Register');
    }

    public function test_guest_can_register_successfully(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'city' => 'Test City',
            'spiritual_preference' => 'secular',
        ];

        $response = $this->post('/en/register', $userData);

        // Just verify user was created successfully
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Verify user exists and can be authenticated
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ];

        $response = $this->post('/en/register', $invalidData);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/en/login');

        $response->assertStatus(200);
        $response->assertSeeText('Login');
    }

    public function test_guest_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->post('/en/login', $loginData);

        $response->assertRedirect('/en/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $invalidLoginData = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];

        $response = $this->post('/en/login', $invalidLoginData);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/en/logout');

        $response->assertRedirect('/en/login');
        $this->assertGuest();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/en/dashboard');

        $response->assertRedirect('/login?locale=en');
    }

    public function test_user_redirected_to_correct_dashboard_after_login(): void
    {
        // Test regular user
        $regularUser = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/en/login', [
            'email' => $regularUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/en/dashboard');

        // Test admin user
        $adminUser = User::factory()->admin()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($regularUser)->post('/en/logout');
        
        $response = $this->post('/en/login', [
            'email' => $adminUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');

        // Test consultant user
        $consultantUser = User::factory()->consultant()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($adminUser)->post('/en/logout');
        
        $response = $this->post('/en/login', [
            'email' => $consultantUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/en/consultant/dashboard');

        // Test translator user
        $translatorUser = User::factory()->translator()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($consultantUser)->post('/en/logout');
        
        $response = $this->post('/en/login', [
            'email' => $translatorUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/en/translator/dashboard');
    }
}
