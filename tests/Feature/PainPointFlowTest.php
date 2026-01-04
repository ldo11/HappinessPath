<?php

namespace Tests\Feature;

use App\Models\PainPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PainPointFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_self_assess_pain_point_score()
    {
        $user = User::factory()->create();
        $painPoint = PainPoint::factory()->create(['status' => 'active']);

        // Attach with score 8
        $response = $this->actingAs($user)
            ->post(route('user.pain-points.store', ['locale' => 'en']), [
                'pain_points' => [
                    [
                        'id' => $painPoint->id,
                        'score' => 8,
                    ]
                ]
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pain_point_user', [
            'user_id' => $user->id,
            'pain_point_id' => $painPoint->id,
            'score' => 8,
        ]);

        // Update score to 5
        $response = $this->actingAs($user)
            ->post(route('user.pain-points.store', ['locale' => 'en']), [
                'pain_points' => [
                    [
                        'id' => $painPoint->id,
                        'score' => 5,
                    ]
                ]
            ]);
            
        $response->assertRedirect();
        $this->assertDatabaseHas('pain_point_user', [
            'user_id' => $user->id,
            'pain_point_id' => $painPoint->id,
            'score' => 5,
        ]);
    }

    public function test_user_can_request_new_pain_point()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('user.pain-points.request', ['locale' => 'en']), [
                'name' => 'My New Pain',
                'description' => 'It hurts a lot',
            ]);

        $response->assertRedirect();
        
        // Note: Name is stored as JSON, so we check using JSON searching or fetching the record
        $painPoint = PainPoint::where('created_by_user_id', $user->id)->first();
        $this->assertNotNull($painPoint);
        // Assuming locale is 'en', the name should be stored as {"en": "My New Pain"} or similar
        // Adjust assertion based on implementation. 
        // The implementation does: 'name' => [app()->getLocale() => $data['name']]
        
        $this->assertEquals('pending', $painPoint->status);
        $this->assertEquals($user->id, $painPoint->created_by_user_id);
        
        // Check JSON content
        $this->assertEquals('My New Pain', $painPoint->getTranslatedName('en'));
    }

    public function test_user_cannot_see_pending_pain_points()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Pending pain point created by User A
        $pendingPainPoint = PainPoint::factory()->create([
            'status' => 'pending', 
            'created_by_user_id' => $userA->id,
            'name' => ['en' => 'Pending Pain'],
        ]);
        
        // Active pain point
        $activePainPoint = PainPoint::factory()->create([
            'status' => 'active',
            'name' => ['en' => 'Active Pain'],
        ]);

        $response = $this->actingAs($userB)
            ->get(route('user.pain-points.index', ['locale' => 'en']));

        $response->assertOk();
        $response->assertSee('Active Pain');
        $response->assertDontSee('Pending Pain');
    }

    public function test_user_can_update_single_pain_point_without_affecting_others()
    {
        // 1. Create a user and 5 active pain points
        $user = User::factory()->create();
        $painPoints = PainPoint::factory()->count(5)->create(['status' => 'active']);
        
        // Attach one pain point initially to verify we don't accidentally detach it if we don't touch it
        $user->painPoints()->attach($painPoints[0]->id, ['score' => 5]);

        // 2. Act as User
        $this->actingAs($user);

        // 3. Scenario: User submits a request to update ONLY ONE pain point (e.g., ID of 3rd pain point, Score 7)
        $targetPainPoint = $painPoints[2]; // 3rd one
        
        $payload = [
            'pain_points' => [
                [
                    'id' => $targetPainPoint->id,
                    'score' => 7
                ]
            ]
        ];

        $response = $this->post(route('user.pain-points.store', ['locale' => 'en']), $payload);

        // 4. Assert: The request succeeds
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        // 5. Assert: ONLY pain point 3 is updated/attached with score 7
        $this->assertDatabaseHas('pain_point_user', [
            'user_id' => $user->id,
            'pain_point_id' => $targetPainPoint->id,
            'score' => 7,
        ]);

        // 6. Assert: The initial pain point (index 0) is STILL attached (syncWithoutDetaching behavior needed)
        $this->assertDatabaseHas('pain_point_user', [
            'user_id' => $user->id,
            'pain_point_id' => $painPoints[0]->id,
            'score' => 5,
        ]);
        
        // 7. Assert: Other pain points are NOT attached
        $this->assertDatabaseMissing('pain_point_user', [
            'user_id' => $user->id,
            'pain_point_id' => $painPoints[1]->id,
        ]);
    }
}
