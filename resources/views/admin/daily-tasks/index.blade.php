@extends('layouts.admin')

@section('title', 'Daily Tasks')
@section('page-title', 'Daily Tasks')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-800">Daily Tasks</h2>
    <a href="{{ route('admin.daily-tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Create</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tasks as $t)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $t->day_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $t->title }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $t->status }}</td>
                        <td class="px-6 py-3 text-sm">
                            <a class="text-blue-600" href="{{ route('admin.daily-tasks.edit', $t) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.daily-tasks.destroy', $t) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
