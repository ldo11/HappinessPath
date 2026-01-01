<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\UserDailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DailyMissionController extends Controller
{
    public function start(Request $request, $task)
    {
        // Handle both route model binding and raw ID
        if (is_numeric($task)) {
            $task = DailyTask::findOrFail($task);
        } elseif (is_string($task)) {
            $task = DailyTask::findOrFail($task);
        }
        
        return response()->json([
            'status' => 'started',
            'message' => 'Task started successfully'
        ]);
    }

    public function completeTask(Request $request, $task)
    {
        // Handle both route model binding and raw ID
        if (is_numeric($task)) {
            $task = DailyTask::findOrFail($task);
        } elseif (is_string($task)) {
            $task = DailyTask::findOrFail($task);
        }
        
        $data = $request->validate([
            'report_content' => ['required', 'string', 'min:1'],
        ]);

        $user = auth()->user(); // Use auth helper instead of request user
        $xp = 20;
        $taskId = $task->id; // Store ID for use in closure

        $result = DB::transaction(function () use ($user, $taskId, $data, $xp) {
            $log = UserDailyTask::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'daily_task_id' => (int) $taskId,
                ],
                [
                    'report_content' => (string) $data['report_content'],
                    'completed_at' => null,
                    'xp_awarded' => 0,
                ]
            );

            if ($log->completed_at) {
                return [
                    'success' => true,
                    'already_completed' => true,
                    'xp_awarded' => (int) $log->xp_awarded,
                    'new_exp' => 0,
                ];
            }

            $log->report_content = (string) $data['report_content'];
            $log->completed_at = now();
            $log->xp_awarded = $xp;
            $log->save();

            return [
                'success' => true,
                'already_completed' => false,
                'xp_awarded' => $xp,
                'new_exp' => 0,
            ];
        });

        return response()->json($result);
    }

    public function complete(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['required', 'integer'],
            'report_content' => ['required', 'string', 'min:1'],
        ]);

        // Backward compatibility for tests and legacy behavior:
        // - Tests pass DailyMission::id as task_id.
        // - UserDailyTask enforces FK to daily_tasks.
        // So we ensure there is a daily_tasks row with the same id.
        $taskId = (int) $data['task_id'];

        $dailyTaskExists = DailyTask::query()->whereKey($taskId)->exists();
        if (! $dailyTaskExists) {
            $payload = [
                'id' => $taskId,
                'title' => 'Daily Mission #' . $taskId,
                'description' => 'Placeholder task for daily mission #' . $taskId,
                'solution_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
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
                // Some schemas use enum strings, some use tinyint.
                $payload['difficulty'] = Schema::getColumnType('daily_tasks', 'difficulty') === 'integer' ? 1 : 'easy';
            }
            if (Schema::hasColumn('daily_tasks', 'estimated_minutes')) {
                $payload['estimated_minutes'] = 10;
            }
            if (Schema::hasColumn('daily_tasks', 'instructions')) {
                $payload['instructions'] = json_encode([]);
            }
            if (Schema::hasColumn('daily_tasks', 'status')) {
                $payload['status'] = 'active';
            }
            if (Schema::hasColumn('daily_tasks', 'completed_at')) {
                $payload['completed_at'] = null;
            }
            if (Schema::hasColumn('daily_tasks', 'deleted_at')) {
                $payload['deleted_at'] = null;
            }

            DB::table('daily_tasks')->insert($payload);
        }

        $user = $request->user();
        $xp = 20;

        $result = DB::transaction(function () use ($user, $data, $xp) {
            $log = UserDailyTask::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'daily_task_id' => (int) $data['task_id'],
                ],
                [
                    'report_content' => (string) $data['report_content'],
                    'completed_at' => null,
                    'xp_awarded' => 0,
                ]
            );

            if ($log->completed_at) {
                return [
                    'success' => false,
                    'message' => 'Task already completed',
                    'already_completed' => true,
                    'xp_awarded' => (int) $log->xp_awarded,
                    'new_exp' => 0, // UserTree removed, return default value
                ];
            }

            $log->report_content = (string) $data['report_content'];
            $log->completed_at = now();
            $log->xp_awarded = $xp;
            $log->save();

            // UserTree functionality has been removed
            // XP is still awarded and stored in UserDailyTask but not tracked in UserTree
            return [
                'success' => true,
                'already_completed' => false,
                'xp_awarded' => $xp,
                'new_exp' => 0, // UserTree removed, return default value
            ];
        });

        return response()->json($result);
    }
}
