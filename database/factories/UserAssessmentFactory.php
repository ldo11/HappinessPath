<?php

namespace Database\Factories;

use App\Models\UserAssessment;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAssessment>
 */
class UserAssessmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAssessment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assessment_id' => Assessment::factory(),
            'answers' => [], // Will be filled in tests
            'total_score' => fake()->numberBetween(30, 150),
            'submission_mode' => fake()->randomElement(['self_review', 'submitted_for_consultation']),
            'consultation_thread_id' => null,
        ];
    }
}
