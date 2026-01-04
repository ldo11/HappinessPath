@extends('layouts.app')

@section('title', $missionSet->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('consultant.mission-sets.index', ['locale' => app()->getLocale()]) }}" class="text-white/60 hover:text-white text-sm flex items-center gap-1 mb-2">
            <i class="fas fa-arrow-left"></i> Back to Mission Sets
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $missionSet->getTranslation('name', app()->getLocale()) }}</h2>
                <p class="text-white/60 mt-1">{{ $missionSet->getTranslation('description', app()->getLocale()) }}</p>
            </div>
            <a href="{{ route('consultant.mission-sets.edit', ['locale' => app()->getLocale(), 'missionSet' => $missionSet]) }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition border border-white/10">
                Edit Settings
            </a>
        </div>
    </div>

    <!-- Daily Missions Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-white/80">Daily Missions Program</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @for($day = 1; $day <= 30; $day++)
                @php
                    $mission = $missionSet->missions->where('day_number', $day)->first();
                @endphp
                
                <div class="bg-white/10 backdrop-blur-xl border {{ $mission ? 'border-emerald-500/30' : 'border-white/10' }} rounded-xl p-4 relative group">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider {{ $mission ? 'text-emerald-400' : 'text-white/40' }}">Day {{ $day }}</span>
                        @if($mission)
                            <div class="flex gap-1">
                                <a href="{{ route('consultant.daily-missions.edit', ['locale' => app()->getLocale(), 'dailyMission' => $mission]) }}" class="p-1 text-white/50 hover:text-white transition">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('consultant.daily-missions.destroy', ['locale' => app()->getLocale(), 'dailyMission' => $mission]) }}" onsubmit="return confirm('Remove this mission?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-white/50 hover:text-red-400 transition">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($mission)
                        <h4 class="text-white font-medium line-clamp-2 mb-1">{{ $mission->getTranslation('title', app()->getLocale()) }}</h4>
                        <div class="text-xs text-white/50 mb-2 line-clamp-2">{{ $mission->getTranslation('description', app()->getLocale()) }}</div>
                        
                        <div class="flex flex-wrap gap-1 mt-auto">
                            @if($mission->is_body) <span class="px-1.5 py-0.5 rounded bg-pink-500/20 text-pink-300 text-[10px] border border-pink-500/20">Body</span> @endif
                            @if($mission->is_mind) <span class="px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 text-[10px] border border-blue-500/20">Mind</span> @endif
                            @if($mission->is_wisdom) <span class="px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 text-[10px] border border-purple-500/20">Wisdom</span> @endif
                            <span class="px-1.5 py-0.5 rounded bg-yellow-500/20 text-yellow-300 text-[10px] border border-yellow-500/20 ml-auto">+{{ $mission->points }} XP</span>
                        </div>
                    @else
                        <div class="h-20 flex flex-col items-center justify-center text-white/20 border-2 border-dashed border-white/10 rounded-lg hover:border-white/30 hover:text-white/40 transition cursor-pointer" onclick="document.getElementById('addMissionModal-{{ $day }}').showModal()">
                            <i class="fas fa-plus mb-1"></i>
                            <span class="text-xs">Add Mission</span>
                        </div>
                    @endif
                </div>

                <!-- Add/Create Mission Modal -->
                <dialog id="addMissionModal-{{ $day }}" class="bg-gray-900 text-white rounded-2xl p-0 backdrop:bg-black/80 w-full max-w-lg border border-white/10">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">Add Mission for Day {{ $day }}</h3>
                        
                        <div class="space-y-4">
                            <!-- Choose Existing Form -->
                            <form method="POST" action="{{ route('consultant.mission-sets.clone-mission', ['locale' => app()->getLocale(), 'missionSet' => $missionSet]) }}">
                                @csrf
                                <input type="hidden" name="day_number" value="{{ $day }}">
                                
                                <div>
                                    <label class="block text-sm font-medium text-white/80 mb-2">Select Mission</label>
                                    <select name="source_mission_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50" required>
                                        <option value="" class="text-gray-900">-- Choose a mission --</option>
                                        @foreach($availableMissions as $availMission)
                                            <option value="{{ $availMission->id }}" class="text-gray-900">
                                                {{ $availMission->getTranslation('title', app()->getLocale()) }} ({{ $availMission->points }} XP)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" onclick="document.getElementById('addMissionModal-{{ $day }}').close()" class="px-4 py-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10">Cancel</button>
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-2 rounded-lg font-medium">Add Selected</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </dialog>
            @endfor
        </div>
    </div>
</div>
@endsection
