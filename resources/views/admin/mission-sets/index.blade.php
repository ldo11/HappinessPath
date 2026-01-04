@extends('layouts.admin')

@section('title', 'Mission Sets')
@section('page-title', 'Mission Sets')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-600 mt-1">Manage 30-day mission programs.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.mission-sets.create') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg shadow transition">
            <i class="fas fa-plus mr-2"></i> Create New Set
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Missions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($missionSets as $set)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $set->getTranslation('name', 'en') }}</div>
                            <div class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $set->getTranslation('description', 'en') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($set->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $set->missions_count }} / 30</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $set->creator->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.mission-sets.show', $set) }}" class="text-indigo-600 hover:text-indigo-900">Manage</a>
                                <a href="{{ route('admin.mission-sets.edit', $set) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                <form method="POST" action="{{ route('admin.mission-sets.destroy', $set) }}" class="inline" onsubmit="return confirm('Delete this mission set?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($missionSets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $missionSets->links() }}
        </div>
    @endif
</div>
@endsection
