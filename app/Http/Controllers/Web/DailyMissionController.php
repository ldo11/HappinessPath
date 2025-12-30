<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserDailyTask;
use App\Models\UserTree;
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
                $tree = UserTree::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'season' => 'spring',
                        'health' => 50,
                        'exp' => 0,
                        'fruits_balance' => 0,
                        'total_fruits_given' => 0,
                    ]
                );

                return [
                    'success' => true,
                    'already_completed' => true,
                    'xp_awarded' => (int) $log->xp_awarded,
                    'new_exp' => (int) $tree->exp,
                ];
            }

            $log->report_content = (string) $data['report_content'];
            $log->completed_at = now();
            $log->xp_awarded = $xp;
            $log->save();

            $tree = UserTree::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'season' => 'spring',
                    'health' => 50,
                    'exp' => 0,
                    'fruits_balance' => 0,
                    'total_fruits_given' => 0,
                ]
            );

            $tree->exp = (int) $tree->exp + $xp;
            $tree->save();

            return [
                'success' => true,
                'already_completed' => false,
                'xp_awarded' => $xp,
                'new_exp' => (int) $tree->exp,
            ];
        });

        return response()->json($result);
    }
}
