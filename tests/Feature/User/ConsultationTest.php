<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\PainPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_consultation_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.consultations.index', ['locale' => 'en']));

        $response->assertStatus(200);
        $response->assertViewIs('consultations.index');
        $response->assertSee('Tư vấn');
    }

    public function test_user_can_access_create_consultation_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.consultations.create', ['locale' => 'en']));

        $response->assertStatus(200);
        $response->assertViewIs('consultations.create');
    }

    public function test_user_can_create_consultation_request(): void
    {
        $user = User::factory()->create();
        $painPoint = PainPoint::factory()->create();

        $data = [
            'title' => 'Test Consultation Request',
            'content' => 'I need help with my happiness path.',
            'pain_point_id' => $painPoint->id,
        ];

        $response = $this->actingAs($user)->post(route('user.consultations.store', ['locale' => 'en']), $data);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('consultation_threads', [
            'user_id' => $user->id,
            'title' => 'Test Consultation Request',
            'content' => 'I need help with my happiness path.',
            'pain_point_id' => $painPoint->id,
        ]);
    }
}
