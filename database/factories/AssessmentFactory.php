<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Assessment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'en' => fake()->sentence(4),
                'vi' => 'Câu ' . fake()->sentence(3),
            ],
            'description' => [
                'en' => fake()->paragraph(2),
                'vi' => fake()->paragraph(2),
            ],
            'status' => fake()->randomElement(['created', 'translated', 'reviewed', 'active', 'special']),
            'created_by' => User::factory(),
        ];
    }
}
