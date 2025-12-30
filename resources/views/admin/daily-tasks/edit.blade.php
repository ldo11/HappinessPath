@extends('layouts.admin')

@section('title', 'Edit Daily Task')
@section('page-title', 'Edit Daily Task')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Daily Task</h2>

<form method="POST" action="{{ route('admin.daily-tasks.update', $task) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm text-gray-700 mb-1">Day Number</label>
        <input type="number" name="day_number" value="{{ $task->day_number }}" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Title</label>
        <input type="text" name="title" value="{{ $task->title }}" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Description</label>
        <textarea name="description" class="w-full border rounded-lg px-3 py-2" rows="3">{{ $task->description }}</textarea>
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Type</label>
        <input type="text" name="type" value="{{ $task->type }}" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Difficulty</label>
        <input type="text" name="difficulty" value="{{ $task->difficulty }}" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Estimated Minutes</label>
        <input type="number" name="estimated_minutes" value="{{ $task->estimated_minutes }}" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border rounded-lg px-3 py-2">
            <option value="active" @selected($task->status === 'active')>active</option>
            <option value="inactive" @selected($task->status === 'inactive')>inactive</option>
        </select>
    </div>

    <div class="pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Save</button>
        <a href="{{ route('admin.daily-tasks.index') }}" class="ml-3 text-gray-700">Cancel</a>
    </div>
</form>
@endsection
