<?php

namespace Database\Factories;

use App\Models\AssessmentOption;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentOption>
 */
class AssessmentOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssessmentOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => AssessmentQuestion::factory(),
            'content' => [
                'en' => fake()->sentence(3),
                'vi' => fake()->sentence(3),
            ],
            'score' => fake()->numberBetween(1, 5),
        ];
    }
}
