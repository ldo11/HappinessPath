<?php

namespace Database\Factories;

use App\Models\ConsultationThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsultationThread>
 */
class ConsultationThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConsultationThread::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(3),
            'related_pain_point_id' => null,
            'status' => fake()->randomElement(['pending', 'active', 'closed']),
            'is_private' => fake()->boolean(),
            'related_assessment_id' => null,
            'pain_point_id' => null,
            'assigned_consultant_id' => null,
            'closed_at' => null,
        ];
    }
}
