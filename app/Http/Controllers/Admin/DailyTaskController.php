<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;

class DailyTaskController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index()
    {
        $tasks = DailyTask::query()->orderBy('day_number')->paginate(30);

        return view('admin.daily-tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.daily-tasks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'day_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'difficulty' => ['required', 'string', 'max:50'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DailyTask::create([
            'day_number' => $data['day_number'],
            'title' => $data['title'],
            'description' => $data['description'],
            'type' => $data['type'],
            'difficulty' => $data['difficulty'],
            'estimated_minutes' => $data['estimated_minutes'] ?? 10,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.daily-tasks.index');
    }

    public function edit(DailyTask $dailyTask)
    {
        return view('admin.daily-tasks.edit', [
            'task' => $dailyTask,
        ]);
    }

    public function update(Request $request, DailyTask $dailyTask)
    {
        $data = $request->validate([
            'day_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'difficulty' => ['required', 'string', 'max:50'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $dailyTask->update([
            'day_number' => $data['day_number'],
            'title' => $data['title'],
            'description' => $data['description'],
            'type' => $data['type'],
            'difficulty' => $data['difficulty'],
            'estimated_minutes' => $data['estimated_minutes'] ?? 10,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.daily-tasks.index');
    }

    public function destroy(DailyTask $dailyTask)
    {
        $dailyTask->delete();

        return redirect()->route('admin.daily-tasks.index');
    }
}
