@extends('layouts.app')

@section('title', $missionSet->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('mission-sets.index') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('mission_sets.back_to_programs') }}
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-6 text-white mb-6">
                <h1 class="text-3xl font-bold mb-3">{{ $missionSet->name }}</h1>
                <p class="text-emerald-100 text-lg">{{ $missionSet->description }}</p>
            </div>

            <!-- Program Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-day text-emerald-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $missionSet->dailyMissions->count() }}</div>
                    <div class="text-sm text-gray-600">{{ __('mission_sets.total_days') }}</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $missionSet->estimated_duration ?? __('mission_sets.variable') }}</div>
                    <div class="text-sm text-gray-600">{{ __('mission_sets.duration') }}</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-trophy text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $missionSet->dailyMissions->sum('points') }}</div>
                    <div class="text-sm text-gray-600">{{ __('mission_sets.total_points') }}</div>
                </div>
            </div>
        </div>

        <!-- Daily Missions -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('mission_sets.daily_missions') }}</h2>
            
            <div class="space-y-4">
                @foreach($missionSet->dailyMissions as $mission)
                    <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition {{ in_array($mission->id, $completedMissions) ? 'bg-emerald-50 border-emerald-200' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <span class="text-emerald-600 font-bold">{{ $mission->day_number }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $mission->title }}</h3>
                                    @if(in_array($mission->id, $completedMissions))
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full">
                                            <i class="fas fa-check mr-1"></i>{{ __('mission_sets.completed') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 mb-4">{{ $mission->description }}</p>
                                
                                <div class="flex flex-wrap items-center gap-3 text-sm">
                                    @if($mission->is_body)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full">
                                            <i class="fas fa-running mr-1"></i>{{ __('dashboard.body') }}
                                        </span>
                                    @endif
                                    @if($mission->is_mind)
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 rounded-full">
                                            <i class="fas fa-brain mr-1"></i>{{ __('dashboard.mind') }}
                                        </span>
                                    @endif
                                    @if($mission->is_wisdom)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">
                                            <i class="fas fa-lightbulb mr-1"></i>{{ __('dashboard.wisdom') }}
                                        </span>
                                    @endif
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                        <i class="fas fa-star mr-1"></i>{{ $mission->points }} {{ __('dashboard.points') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex gap-4">
            <a href="{{ route('mission-sets.index') }}" 
               class="flex-1 text-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition font-medium">
                {{ __('mission_sets.back_to_programs') }}
            </a>
            @if(!in_array($missionSet->id, [Auth::user()->active_mission_set_id]))
                <form action="{{ route('mission-sets.assign', $missionSet) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full emerald-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-medium">
                        {{ __('mission_sets.start_this_program') }}
                    </button>
                </form>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 text-center emerald-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-medium">
                    {{ __('mission_sets.continue_program') }}
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
