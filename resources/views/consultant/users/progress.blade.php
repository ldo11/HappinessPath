@extends('layouts.app')

@section('title', 'User Progress: ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="mb-6">
        <a href="{{ route('consultant.users.index', [app()->getLocale()]) }}" class="text-white/60 hover:text-white text-sm flex items-center gap-1 mb-2">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <span>{{ $user->name }}</span>
            <span class="text-base font-normal text-white/50 bg-white/10 px-2 py-0.5 rounded-lg">{{ ucfirst($user->role) }}</span>
        </h2>
        <div class="text-white/60 text-sm">{{ $user->email }} • Joined {{ $user->created_at->format('M d, Y') }}</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar / Controls -->
        <div class="space-y-6">
            <!-- Current Status Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Current Program</h3>
                
                @if($user->activeMissionSet)
                    <div class="mb-4">
                        <div class="text-emerald-400 font-bold text-lg leading-tight">{{ $user->activeMissionSet->getTranslation('name', app()->getLocale()) }}</div>
                        <div class="text-white/60 text-xs mt-1">{{ $user->activeMissionSet->getTranslation('description', app()->getLocale()) }}</div>
                    </div>

                    <div class="flex items-center justify-between bg-white/5 rounded-xl p-3 mb-4">
                        <div>
                            <div class="text-white/40 text-xs uppercase tracking-wider">Current Day</div>
                            <div class="text-white font-mono text-2xl">{{ $currentDay }} <span class="text-base text-white/40">/ 30</span></div>
                        </div>
                        <div>
                            <div class="text-white/40 text-xs uppercase tracking-wider text-right">Started</div>
                            <div class="text-white text-sm">{{ $user->mission_started_at?->format('M d, Y') ?? 'N/A' }}</div>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-white/5 text-white/50 text-center text-sm italic mb-4">
                        No program currently assigned.
                    </div>
                @endif

                <hr class="border-white/10 my-4">

                <form method="POST" action="{{ route('consultant.users.assign', [app()->getLocale(), $user->id]) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-white/80 mb-1">Change Program</label>
                            <select name="mission_set_id" class="w-full bg-black/20 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 [&>option]:text-gray-900">
                                @foreach($missionSets as $set)
                                    <option value="{{ $set->id }}" {{ $user->active_mission_set_id == $set->id ? 'selected' : '' }}>
                                        {{ $set->getTranslation('name', app()->getLocale()) }} 
                                        {{ $set->is_default ? '(Default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-start gap-2">
                            <input type="checkbox" name="reset_progress" id="reset_progress" value="1" class="mt-1 rounded bg-black/20 border-white/20 text-emerald-500 focus:ring-emerald-500/50" checked>
                            <label for="reset_progress" class="text-sm text-white/70 leading-snug">
                                Reset progress to Day 1
                                <span class="block text-white/30 text-xs">Start date will be updated to today.</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-white font-medium py-2 rounded-lg transition text-sm">
                            {{ $user->activeMissionSet ? 'Update Program' : 'Assign Program' }}
                        </button>
                        
                        <!-- Test: Show form action for debugging -->
                        <p class="text-xs text-white/30 mt-2">Form action: {{ route('consultant.users.assign', [app()->getLocale(), $user->id]) }}</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="lg:col-span-2">
            <h3 class="text-lg font-semibold text-white mb-4">30-Day Journey</h3>
            
            @if($user->activeMissionSet)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3">
                    @for($day = 1; $day <= 30; $day++)
                        @php
                            $mission = $user->activeMissionSet->missions->where('day_number', $day)->first();
                            $status = 'locked';
                            if ($day < $currentDay) $status = 'completed';
                            if ($day == $currentDay) $status = 'current';
                            if ($day > $currentDay) $status = 'future';
                            
                            $bgClass = match($status) {
                                'completed' => 'bg-emerald-500/20 border-emerald-500/30 text-emerald-100',
                                'current' => 'bg-white text-gray-900 border-white ring-4 ring-emerald-500/30',
                                'future' => 'bg-white/5 border-white/10 text-white/40',
                                default => 'bg-white/5 border-white/10 text-white/40',
                            };
                        @endphp
                        
                        <div class="rounded-xl border p-3 relative group {{ $bgClass }} min-h-[100px] flex flex-col">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-bold uppercase">Day {{ $day }}</span>
                                @if($status === 'completed')
                                    <i class="fas fa-check-circle text-emerald-400 text-xs"></i>
                                @endif
                            </div>
                            
                            @if($mission)
                                <div class="mt-auto text-sm font-medium leading-tight line-clamp-2 {{ $status === 'current' ? 'text-gray-900' : '' }}">
                                    {{ $mission->getTranslation('title', app()->getLocale()) }}
                                </div>
                                <div class="text-[10px] mt-1 opacity-70 {{ $status === 'current' ? 'text-gray-700' : '' }}">
                                    {{ $mission->points }} XP
                                </div>
                            @else
                                <div class="mt-auto text-xs italic opacity-50">No mission</div>
                            @endif
                        </div>
                    @endfor
                </div>
            @else
                <div class="rounded-2xl bg-white/5 border border-white/10 p-8 text-center text-white/40">
                    <i class="fas fa-layer-group text-4xl mb-3 opacity-50"></i>
                    <p>Assign a mission set to see the journey grid.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
