<?php

namespace Database\Factories;

use App\Models\DailyTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTask>
 */
class DailyTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DailyTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_number' => fake()->numberBetween(1, 90),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(2),
            'type' => fake()->randomElement(['mindfulness', 'physical', 'emotional']),
            'estimated_minutes' => fake()->numberBetween(5, 45),
            'solution_id' => null,
            'instructions' => fake()->paragraph(1),
            'status' => 'active',
            'completed_at' => null,
        ];
    }
}
