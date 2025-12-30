<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserDailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyMissionController extends Controller
{
    public function complete(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['required', 'integer', 'exists:daily_tasks,id'],
            'report_content' => ['required', 'string', 'min:1'],
        ]);

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
                    'success' => true,
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
