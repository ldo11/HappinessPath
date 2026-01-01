<?php

namespace Database\Factories;

use App\Models\UserDailyTask;
use App\Models\User;
use App\Models\DailyTask;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;

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

    public function configure()
    {
        return $this->afterMaking(function (UserDailyTask $userDailyTask) {
            $id = $userDailyTask->daily_task_id;
            if (is_int($id) || (is_string($id) && ctype_digit($id))) {
                $taskId = (int) $id;
                if (!DailyTask::query()->whereKey($taskId)->exists()) {
                    $payload = [
                        'id' => $taskId,
                        'title' => 'Daily Task #' . $taskId,
                        'description' => 'Auto-created for tests',
                        'solution_id' => null,
                    ];

                    if (Schema::hasColumn('daily_tasks', 'day')) {
                        $payload['day'] = 1;
                    }
                    if (Schema::hasColumn('daily_tasks', 'day_number')) {
                        $payload['day_number'] = 1;
                    }

                    if (Schema::hasColumn('daily_tasks', 'type')) {
                        $payload['type'] = 'mindfulness';
                    }
                    if (Schema::hasColumn('daily_tasks', 'difficulty')) {
                        $payload['difficulty'] = Schema::getColumnType('daily_tasks', 'difficulty') === 'integer' ? 1 : 'easy';
                    }
                    if (Schema::hasColumn('daily_tasks', 'estimated_minutes')) {
                        $payload['estimated_minutes'] = 10;
                    }
                    if (Schema::hasColumn('daily_tasks', 'instructions')) {
                        $payload['instructions'] = [];
                    }
                    if (Schema::hasColumn('daily_tasks', 'status')) {
                        $payload['status'] = 'active';
                    }
                    if (Schema::hasColumn('daily_tasks', 'completed_at')) {
                        $payload['completed_at'] = null;
                    }

                    DailyTask::query()->create($payload);
                }
            }
        });
    }
}
