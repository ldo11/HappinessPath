@extends('layouts.admin')

@section('title', 'Daily Missions')
@section('page-title', 'Daily Missions')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-800">Daily Missions</h2>
    <a href="{{ route('admin.daily-missions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Create</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($missions as $m)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-800">
                            <div class="font-medium">{{ $m->title }}</div>
                            @if($m->description)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($m->description, 120) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $m->points }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">
                            {{ $m->createdBy?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-3 text-sm">
                            <a class="text-blue-600" href="{{ route('admin.daily-missions.edit', $m) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.daily-missions.destroy', $m) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2" onclick="return confirm('Delete this daily mission?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $missions->links() }}
    </div>
</div>
@endsection
