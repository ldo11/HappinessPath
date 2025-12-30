<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentQuestion>
 */
class AssessmentQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssessmentQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'content' => [
                'en' => fake()->sentence(6) . '?',
                'vi' => fake()->sentence(5) . '?',
            ],
            'type' => fake()->randomElement(['single_choice', 'multi_choice']),
            'order' => fake()->numberBetween(1, 50),
            'pillar_group_new' => fake()->randomElement(['body', 'mind', 'wisdom']),
        ];
    }
}
