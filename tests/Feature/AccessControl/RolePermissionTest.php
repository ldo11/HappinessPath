<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $consultant;
    private User $translator;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->consultant = User::factory()->create(['role' => 'consultant']);
        $this->translator = User::factory()->create(['role' => 'translator']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function admin_can_access_admin_users_page()
    {
        $response = $this->actingAs($this->admin)
            ->get('/en/admin/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_gets_forbidden_when_accessing_admin_users()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/en/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function consultant_can_access_admin_users_page()
    {
        $response = $this->actingAs($this->consultant)
            ->get('/en/admin/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function translator_gets_forbidden_when_accessing_admin_users()
    {
        $response = $this->actingAs($this->translator)
            ->get('/en/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function translator_can_access_translator_dashboard()
    {
        $response = $this->actingAs($this->translator)
            ->get('/en/translator/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function translator_cannot_access_admin_videos()
    {
        $response = $this->actingAs($this->translator)
            ->get('/en/admin/videos');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_admin_videos()
    {
        $response = $this->actingAs($this->admin)
            ->get('/en/admin/videos');

        $response->assertStatus(200);
    }

    /** @test */
    public function consultant_can_access_consultant_dashboard()
    {
        $response = $this->actingAs($this->consultant)
            ->get('/en/consultant/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_consultant_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/en/consultant/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_translator_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/en/translator/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get('/en/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/en/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function consultant_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->consultant)
            ->get('/en/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function translator_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->translator)
            ->get('/en/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_access_regular_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/en/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_regular_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get('/en/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function consultant_can_access_regular_dashboard()
    {
        $response = $this->actingAs($this->consultant)
            ->get('/en/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function translator_can_access_regular_dashboard()
    {
        $response = $this->actingAs($this->translator)
            ->get('/en/dashboard');

        $response->assertStatus(200);
    }

    //    /** @test */
//    public function guest_cannot_access_any_admin_pages()
//    {
//        $response = $this->get('/en/admin/users');
//        $response->assertRedirect('/en/login');
//
//        $response = $this->get('/en/admin/dashboard');
//        $response->assertRedirect('/en/login');
//
//        $response = $this->get('/en/admin/videos');
//        $response->assertRedirect('/en/login');
//    }

    //    /** @test */
//    public function guest_cannot_access_role_specific_pages()
//    {
//        $response = $this->get('/en/translator/dashboard');
//        $response->assertRedirectContains('/login');
//
//        $response = $this->get('/en/consultant/dashboard');
//        $response->assertRedirectContains('/login');
//    }

    /** @test */
    public function role_attribute_returns_correct_values()
    {
        $this->assertEquals('admin', $this->admin->role);
        $this->assertEquals('consultant', $this->consultant->role);
        $this->assertEquals('translator', $this->translator->role);
        $this->assertEquals('user', $this->regularUser->role);
    }

    /** @test */
    public function legacy_role_mapping_works_correctly()
    {
        // Test legacy role mapping
        $legacyUser = User::factory()->create(['role' => 'volunteer']);
        $this->assertEquals('translator', $legacyUser->role);

        $legacyMember = User::factory()->create(['role' => 'member']);
        $this->assertEquals('user', $legacyMember->role);
    }
}
