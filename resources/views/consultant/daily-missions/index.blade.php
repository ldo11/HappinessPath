@extends('layouts.app')

@section('title', 'Daily Missions')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Daily Missions</h2>
        <a href="{{ route('consultant.daily-missions.create', ['locale' => app()->getLocale()]) }}" class="bg-white text-gray-900 hover:bg-gray-100 px-4 py-2 rounded-lg">Create</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Points</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($missions as $m)
                        <tr>
                            <td class="px-6 py-3 text-sm text-white">
                                <div class="font-medium">{{ $m->title }}</div>
                                @if($m->description)
                                    <div class="text-xs text-white/60 mt-1">{{ Str::limit($m->description, 120) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-white">{{ $m->points }}</td>
                            <td class="px-6 py-3 text-sm text-white">{{ $m->createdBy?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm">
                                <a class="text-emerald-300 hover:text-emerald-200" href="{{ route('consultant.daily-missions.edit', ['locale' => app()->getLocale(), 'dailyMission' => $m]) }}">Edit</a>
                                <form method="POST" action="{{ route('consultant.daily-missions.destroy', ['locale' => app()->getLocale(), 'dailyMission' => $m]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-300 hover:text-red-200 ml-2" onclick="return confirm('Delete this daily mission?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/10">
            {{ $missions->links() }}
        </div>
    </div>
</div>
@endsection
