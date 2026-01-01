<?php

namespace Tests\Feature;

use App\Models\ConsultationReply;
use App\Models\ConsultationThread;
use App\Models\PainPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function assigned_consultant_can_see_thread_admin_can_see_thread_other_consultant_cannot(): void
    {
        $painPoint = PainPoint::query()->create([
            'name' => 'Career',
            'category' => 'wisdom',
            'icon' => null,
            'description' => 'Career and work-related challenges.',
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $consultantA = User::factory()->create([
            'role' => 'consultant',
            'is_available' => true,
        ]);
        $consultantB = User::factory()->create([
            'role' => 'consultant',
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->post('/en/consultations', [
                'title' => 'Need help',
                'content' => 'Please advise',
                'pain_point_id' => $painPoint->id,
                'assigned_consultant_id' => $consultantA->id,
            ])
            ->assertRedirect();

        $thread = ConsultationThread::query()->firstOrFail();

        $this->actingAs($consultantA)
            ->get('/en/consultations/'.$thread->id)
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/en/consultations/'.$thread->id)
            ->assertStatus(200);

        $this->actingAs($consultantB)
            ->get('/en/consultations/'.$thread->id)
            ->assertStatus(403);
    }

    /** @test */
    public function only_available_consultants_appear_in_create_consultation_dropdown(): void
    {
        $painPoint = PainPoint::query()->create([
            'name' => 'Love',
            'category' => 'mind',
            'icon' => null,
            'description' => 'Relationship and love-related challenges.',
        ]);

        $user = User::factory()->create(['role' => 'user']);

        $consultantA = User::factory()->create([
            'role' => 'consultant',
            'is_available' => false,
            'name' => 'Consultant A',
        ]);
        $consultantB = User::factory()->create([
            'role' => 'consultant',
            'is_available' => true,
            'name' => 'Consultant B',
        ]);
        $consultantC = User::factory()->create([
            'role' => 'consultant',
            'is_available' => true,
            'name' => 'Consultant C',
        ]);

        $response = $this->actingAs($user)->get('/en/consultations/create');

        $response->assertStatus(200);
        $response->assertSee('Consultant B');
        $response->assertSee('Consultant C');
        $response->assertDontSee('Consultant A');

        // sanity: pain point should be present
        $response->assertSee('Love');

        $this->assertDatabaseHas('users', ['id' => $consultantA->id, 'is_available' => 0]);
        $this->assertDatabaseHas('users', ['id' => $consultantB->id, 'is_available' => 1]);
        $this->assertDatabaseHas('users', ['id' => $consultantC->id, 'is_available' => 1]);
    }

    /** @test */
    public function user_can_close_thread_after_consultant_replies(): void
    {
        $painPoint = PainPoint::query()->create([
            'name' => 'Family',
            'category' => 'mind',
            'icon' => null,
            'description' => 'Family and parenting-related challenges.',
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $consultant = User::factory()->create([
            'role' => 'consultant',
            'is_available' => true,
        ]);

        $thread = ConsultationThread::query()->create([
            'user_id' => $user->id,
            'title' => 'Help please',
            'content' => 'Initial message',
            'pain_point_id' => $painPoint->id,
            'related_pain_point_id' => $painPoint->id,
            'assigned_consultant_id' => $consultant->id,
            'status' => 'open',
            'is_private' => true,
        ]);

        $this->actingAs($consultant)
            ->post('/en/consultations/'.$thread->id.'/replies', [
                'content' => 'My advice...',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('consultation_replies', [
            'thread_id' => $thread->id,
            'user_id' => $consultant->id,
        ]);

        $this->actingAs($user)
            ->post('/en/consultations/'.$thread->id.'/close')
            ->assertRedirect();

        $thread->refresh();

        $this->assertSame('closed', $thread->status);
        $this->assertNotNull($thread->closed_at);
    }
}
