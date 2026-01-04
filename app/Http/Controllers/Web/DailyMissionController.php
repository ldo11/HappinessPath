<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\DailyMission;
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

        $taskId = (int) $data['task_id'];

        // JIT Creation Logic with Validation
        // We only auto-create a DailyTask placeholder if a valid DailyMission exists with this ID.
        // This supports the legacy behavior where DailyMission IDs are passed as task_id,
        // while strictly preventing arbitrary integer completions.
        $dailyTaskExists = DailyTask::query()->whereKey($taskId)->exists();
        
        if (! $dailyTaskExists) {
            // Check if it's a valid DailyMission
            $missionExists = DailyMission::where('id', $taskId)->exists();
            
            if (!$missionExists) {
                // If neither exists, fail validation
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'task_id' => ['The selected task id is invalid (not found in tasks or missions).'],
                ]);
            }

            // Auto-create placeholder for valid Mission
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
        
        // Determine XP to award
        $xp = 20; // Default
        $mission = DailyMission::find($taskId);
        if ($mission) {
            $xp = $mission->points ?? 20;
        }

        $result = DB::transaction(function () use ($user, $data, $xp, $taskId) {
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
                    'success' => false,
                    'message' => 'Task already completed',
                    'already_completed' => true,
                    'xp_awarded' => (int) $log->xp_awarded,
                    'new_exp' => 0, 
                ];
            }

            $log->report_content = (string) $data['report_content'];
            $log->completed_at = now();
            $log->xp_awarded = $xp;
            $log->save();

            // Award XP to specific column based on task type
            $task = DailyTask::find($taskId);
            if ($task) {
                $type = $task->type ?? 'mindfulness';
                $column = match($type) {
                    'physical' => 'xp_body',
                    'wisdom' => 'xp_wisdom',
                    default => 'xp_mind',
                };
                $user->increment($column, $xp);
            } else {
                // Fallback if task not found (should be rare due to prior checks)
                $user->increment('xp_mind', $xp);
            }

            return [
                'success' => true,
                'already_completed' => false,
                'xp_awarded' => $xp,
                'new_exp' => $user->fresh()->xp_mind, // Return relevant XP or total? Just returning one for UI mostly.
            ];
        });

        return response()->json($result);
    }
}
