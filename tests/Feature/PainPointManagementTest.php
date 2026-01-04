<?php

namespace Tests\Feature;

use App\Models\PainPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PainPointManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approval_workflow()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userB = User::factory()->create();

        $pendingPainPoint = PainPoint::factory()->create([
            'status' => 'pending',
            'name' => ['en' => 'Pending Pain'],
        ]);

        // Act as Admin -> Approve
        $response = $this->actingAs($admin)
            ->post(route('user.admin.pain-points.approve', ['locale' => 'en', 'id' => $pendingPainPoint->id]), [
                'name' => 'Approved Pain', // Admin can rename
                'category' => 'mind',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('pain_points', [
            'id' => $pendingPainPoint->id,
            'status' => 'active',
            'category' => 'mind',
        ]);
        
        // Check name update in JSON (assuming default locale en)
        $pendingPainPoint->refresh();
        $this->assertEquals('Approved Pain', $pendingPainPoint->getTranslatedName('en'));

        // Act as User B -> Request list
        $response = $this->actingAs($userB)
            ->get(route('user.pain-points.index', ['locale' => 'en']));

        $response->assertOk();
        $response->assertSee('Approved Pain');
    }

    public function test_consultant_can_reject_pain_point()
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        
        $pendingPainPoint = PainPoint::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->actingAs($consultant)
            ->post(route('user.admin.pain-points.reject', ['locale' => 'en', 'id' => $pendingPainPoint->id]));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('pain_points', [
            'id' => $pendingPainPoint->id,
            'status' => 'rejected',
        ]);
    }

    public function test_consultant_can_view_user_pain_data()
    {
        $consultant = User::factory()->create(['role' => 'consultant']);
        $userA = User::factory()->create(['name' => 'User A']);
        
        $painPointX = PainPoint::factory()->create(['name' => ['en' => 'Pain X']]);
        
        // Attach Pain Point X to User A with Score 9
        $userA->painPoints()->attach($painPointX->id, ['score' => 9]);

        // Act as Consultant -> Visit User A detail page
        $response = $this->actingAs($consultant)
            ->get(route('user.admin.users.edit', ['locale' => 'en', 'user' => $userA->id]));

        $response->assertOk();
        $response->assertSee('Pain X');
        $response->assertSee('Score: 9');
    }

    public function test_translator_visibility()
    {
        $translator = User::factory()->create(['role' => 'translator']);
        
        $activePainPoint = PainPoint::factory()->create(['status' => 'active', 'name' => ['en' => 'Active Pain']]);
        $pendingPainPoint = PainPoint::factory()->create(['status' => 'pending', 'name' => ['en' => 'Pending Pain']]);

        // Act as Translator -> Visit Translation Matrix/List
        $response = $this->actingAs($translator)
            ->get(route('user.translator.pain-points.index', ['locale' => 'en']));

        $response->assertOk();
        $response->assertSee('Active Pain');
        $response->assertSee('Pending Pain');
    }
}
