<?php

namespace Database\Factories;

use App\Models\UserDailyTask;
use App\Models\User;
use App\Models\DailyTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDailyTask>
 */
class UserDailyTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserDailyTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'daily_task_id' => DailyTask::factory(),
            'report_content' => fake()->paragraph(3),
            'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'xp_awarded' => fake()->numberBetween(5, 25),
        ];
    }
}
