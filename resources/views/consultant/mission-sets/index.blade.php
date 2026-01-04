@extends('layouts.app')

@section('title', 'Mission Sets')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Mission Sets</h2>
            <p class="text-sm text-white/60 mt-1">Manage 30-day mission programs.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('consultant.mission-sets.create', ['locale' => app()->getLocale()]) }}" class="bg-white text-gray-900 hover:bg-gray-100 px-4 py-2 rounded-lg">Create New Set</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Missions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($missionSets as $set)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $set->getTranslation('name', app()->getLocale()) }}</div>
                                <div class="text-sm text-white/60 mt-1 line-clamp-1">{{ $set->getTranslation('description', app()->getLocale()) }}</div>
                            </td>
                            <td class="px-6 py-4 text-white/80">
                                <span class="px-2 py-1 rounded-full text-xs border border-white/20 bg-white/5">
                                    {{ ucfirst($set->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-white/80">{{ $set->missions_count }} / 30</td>
                            <td class="px-6 py-4 text-white/80 text-sm">{{ $set->creator->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('consultant.mission-sets.show', ['locale' => app()->getLocale(), 'missionSet' => $set]) }}" class="text-white/80 hover:text-white">Manage</a>
                                    <a href="{{ route('consultant.mission-sets.edit', ['locale' => app()->getLocale(), 'missionSet' => $set]) }}" class="text-emerald-300 hover:text-emerald-200">Edit</a>
                                    <form method="POST" action="{{ route('consultant.mission-sets.destroy', ['locale' => app()->getLocale(), 'missionSet' => $set]) }}" class="inline" onsubmit="return confirm('Delete this mission set?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-300 hover:text-red-200">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($missionSets->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $missionSets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
