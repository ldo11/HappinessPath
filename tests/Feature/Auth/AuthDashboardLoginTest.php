<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LanguageLineSeeder;
use Database\Seeders\TestUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthDashboardLoginTest extends TestCase
{
    use RefreshDatabase;

    private function getSeedPassword(): string
    {
        return 'password';
    }

    private function assertPageHasGreetingAndOk(string $path, string $expectedName): void
    {
        $page = $this->followingRedirects()->get($path);
        $page->assertOk();
        $page->assertSee('Hi ' . $expectedName, false);
    }

    private function login(string $email, string $password): void
    {
        $this->seed(TestUsersSeeder::class);

        $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $this->seed(TestUsersSeeder::class);
        $this->seed(LanguageLineSeeder::class);

        $admin = User::where('email', 'admin@happiness.test')->firstOrFail();

        $response = $this->post('/en/login', [
            'email' => 'admin@happiness.test',
            'password' => $this->getSeedPassword(),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($admin);

        $response->assertRedirect('/en/admin/dashboard');

        $this->assertPageHasGreetingAndOk('/en/admin/dashboard', 'Admin User');
        $this->get('/en/admin/dashboard')->assertSee('Admin Dashboard', false);

        $assessment = $this->get('/en/admin/assessment-questions');
        $assessment->assertOk();
        $assessment->assertSee('Assessment Questions', false);
        $assessment->assertSee('Create', false);

        $painPoints = $this->get('/en/admin/pain-points');
        $painPoints->assertOk();
        $painPoints->assertSee('Pain Points', false);
        $painPoints->assertSee('Category', false);

        $dailyTasks = $this->get('/en/admin/daily-tasks');
        $dailyTasks->assertOk();
        $dailyTasks->assertSee('Daily Tasks', false);
        $dailyTasks->assertSee('Create', false);
    }

    public function test_user_can_login_and_view_dashboard(): void
    {
        $this->seed(TestUsersSeeder::class);
        $this->seed(LanguageLineSeeder::class);

        $user = User::where('email', 'user@happiness.test')->firstOrFail();

        $response = $this->post('/en/login', [
            'email' => 'user@happiness.test',
            'password' => $this->getSeedPassword(),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);

        $response->assertRedirect('/en/dashboard');

        $this->assertPageHasGreetingAndOk('/en/dashboard', 'Regular User');

        $dashboard = $this->get('/en/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee('Hi Regular User', false);
        $dashboard->assertSeeInOrder([
            '<div class="min-h-screen',
        ], false);

        $content = $dashboard->getContent();
        $this->assertTrue(
            str_contains($content, "Today's Practice")
                || str_contains($content, 'Today&#039;s Practice')
                || str_contains($content, 'Thực hành hôm nay'),
            'Dashboard should contain Today Practice section heading'
        );
        
        // Virtue Tree section was removed from dashboard
        // This test is updated to reflect the new layout
        $this->assertTrue(
            !str_contains($content, 'My Virtue Tree') && !str_contains($content, 'Cây Đức Hạnh của tôi'),
            'Dashboard should not contain Virtue Tree section (it was removed)'
        );

        $dashboard->assertSee('/en/assessments', false);
    }

    public function test_translator_can_login_and_view_dashboard(): void
    {
        $this->seed(TestUsersSeeder::class);
        $this->seed(LanguageLineSeeder::class);

        $translator = User::where('email', 'translator@happiness.test')->firstOrFail();

        $response = $this->post('/en/login', [
            'email' => 'translator@happiness.test',
            'password' => $this->getSeedPassword(),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($translator);

        $response->assertRedirect('/en/translator/dashboard');

        $this->assertPageHasGreetingAndOk('/en/translator/dashboard', 'Translator User');
    }
}
