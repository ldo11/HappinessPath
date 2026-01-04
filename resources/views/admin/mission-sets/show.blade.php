@extends('layouts.admin')

@section('title', $missionSet->name)
@section('page-title', $missionSet->getTranslation('name', 'en'))

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.mission-sets.index') }}" class="text-gray-600 hover:text-gray-900 text-sm flex items-center gap-1 mb-2">
                <i class="fas fa-arrow-left"></i> Back to Mission Sets
            </a>
            <p class="text-gray-600">{{ $missionSet->getTranslation('description', 'en') }}</p>
        </div>
        <a href="{{ route('admin.mission-sets.edit', $missionSet) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm transition">
            <i class="fas fa-cog mr-2"></i> Settings
        </a>
    </div>

    <!-- Daily Missions Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Daily Missions Program</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @for($day = 1; $day <= 30; $day++)
                @php
                    $mission = $missionSet->missions->where('day_number', $day)->first();
                @endphp
                
                <div class="bg-white border {{ $mission ? 'border-indigo-200 ring-1 ring-indigo-50' : 'border-gray-200 border-dashed' }} rounded-lg p-4 relative group hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider {{ $mission ? 'text-indigo-600' : 'text-gray-400' }}">Day {{ $day }}</span>
                        @if($mission)
                            <div class="flex gap-1">
                                <a href="{{ route('admin.daily-missions.edit', $mission) }}" class="p-1 text-gray-400 hover:text-indigo-600 transition">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.daily-missions.destroy', $mission) }}" onsubmit="return confirm('Remove this mission?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($mission)
                        <h4 class="text-gray-900 font-medium line-clamp-2 mb-1">{{ $mission->getTranslation('title', 'en') }}</h4>
                        <div class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $mission->getTranslation('description', 'en') }}</div>
                        
                        <div class="flex flex-wrap gap-1 mt-auto">
                            @if($mission->is_body) <span class="px-1.5 py-0.5 rounded bg-pink-100 text-pink-800 text-[10px]">Body</span> @endif
                            @if($mission->is_mind) <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px]">Mind</span> @endif
                            @if($mission->is_wisdom) <span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px]">Wisdom</span> @endif
                            <span class="px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[10px] ml-auto">+{{ $mission->points }} XP</span>
                        </div>
                    @else
                        <div class="h-20 flex flex-col items-center justify-center text-gray-400 cursor-pointer hover:text-indigo-600 transition" onclick="document.getElementById('addMissionModal-{{ $day }}').showModal()">
                            <i class="fas fa-plus mb-1"></i>
                            <span class="text-xs">Add Mission</span>
                        </div>
                    @endif
                </div>

                <!-- Add/Create Mission Modal -->
                <dialog id="addMissionModal-{{ $day }}" class="bg-white rounded-lg p-0 w-full max-w-lg shadow-xl backdrop:bg-black/50">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Add Mission for Day {{ $day }}</h3>
                        
                        <form method="POST" action="{{ route('admin.daily-missions.store') }}">
                            @csrf
                            <input type="hidden" name="mission_set_id" value="{{ $missionSet->id }}">
                            <input type="hidden" name="day_number" value="{{ $day }}">

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">XP Points</label>
                                        <input type="number" name="points" value="10" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Tags</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_body" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">Body</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_mind" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">Mind</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_wisdom" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">Wisdom</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" onclick="document.getElementById('addMissionModal-{{ $day }}').close()" class="px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100">Cancel</button>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium shadow-sm">Create Mission</button>
                            </div>
                        </form>
                    </div>
                </dialog>
            @endfor
        </div>
    </div>
</div>
@endsection
