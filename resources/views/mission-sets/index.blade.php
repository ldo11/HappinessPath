@extends('layouts.app')

@section('title', __('mission_sets.browse_programs'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                {{ __('mission_sets.choose_program') }}
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('mission_sets.program_description') }}
            </p>
        </div>

        <!-- Mission Sets Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($missionSets as $missionSet)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white">
                        <h3 class="text-2xl font-bold mb-2">{{ $missionSet->name }}</h3>
                        <p class="text-emerald-100">{{ $missionSet->description }}</p>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Stats -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ $missionSet->dailyMissions->count() }} {{ __('mission_sets.days') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $missionSet->estimated_duration ?? __('mission_sets.variable') }}
                            </div>
                        </div>

                        <!-- Pillars -->
                        @if($missionSet->dailyMissions->isNotEmpty())
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($missionSet->dailyMissions->contains('is_body', true))
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                            <i class="fas fa-running mr-1"></i>{{ __('dashboard.body') }}
                                        </span>
                                    @endif
                                    @if($missionSet->dailyMissions->contains('is_mind', true))
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs rounded-full">
                                            <i class="fas fa-brain mr-1"></i>{{ __('dashboard.mind') }}
                                        </span>
                                    @endif
                                    @if($missionSet->dailyMissions->contains('is_wisdom', true))
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                                            <i class="fas fa-lightbulb mr-1"></i>{{ __('dashboard.wisdom') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <a href="{{ route('mission-sets.show', $missionSet) }}" 
                               class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-medium">
                                {{ __('mission_sets.view_details') }}
                            </a>
                            <form action="{{ route('mission-sets.assign', $missionSet) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="flex-1 emerald-gradient text-white px-4 py-2 rounded-lg hover:shadow-lg transition font-medium">
                                    {{ __('mission_sets.start_program') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($missionSets->isEmpty())
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-compass text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('mission_sets.no_programs_available') }}
                </h3>
                <p class="text-gray-600">
                    {{ __('mission_sets.check_back_later') }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
