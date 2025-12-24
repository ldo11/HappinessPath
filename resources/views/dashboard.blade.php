@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('content')
<div class="min-h-screen px-4 sm:px-6 lg:px-8 py-6">
    <!-- Responsive Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Mobile Order 1: Today's Task (Action) -->
        <div class="md:col-span-1 lg:col-span-2 order-1 md:order-1">
            <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 spiritual-font mb-1">
                            {{ __('dashboard.today_practice') }}
                        </h2>
                        <p class="text-gray-600">{{ __('dashboard.day_journey', ['day' => $userJourney->current_day]) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-calendar-day text-emerald-500"></i>
                            <span class="text-sm text-gray-600">{{ $todayTask->estimated_minutes ?? 10 }} {{ __('dashboard.minutes') }}</span>
                        </div>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ ($userJourney->current_day / 30) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Task Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-blue-50 rounded-xl p-6 border border-emerald-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $todayTask->type === 'mindfulness' ? 'brain' : 
                                           ($todayTask->type === 'physical' ? 'running' : 'heart') }} 
                               text-white text-lg"></i>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                {{ $todayTask->title }}
                            </h3>
                            <p class="text-gray-600 mb-4 leading-relaxed">
                                {{ $todayTask->description }}
                            </p>

                            @php
                                $instructionsList = [];
                                if (isset($todayTask->instructions) && is_array($todayTask->instructions)) {
                                    $instructionsList = $todayTask->instructions;
                                    if (isset($todayTask->instructions['content'])) {
                                        $instructionsList = [];
                                    }
                                }
                            @endphp

                            @if(count($instructionsList) > 0)
                                <div class="bg-white/70 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-gray-800 mb-2">{{ __('dashboard.instructions') }}</h4>
                                    <ul class="space-y-1 text-sm text-gray-600">
                                        @foreach($instructionsList as $instruction)
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-emerald-500 mr-2 mt-0.5"></i>
                                                <span>{{ $instruction }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-signal mr-1"></i>
                                        {{ $todayTask->difficulty === 'easy' ? __('dashboard.easy') : 
                                           ($todayTask->difficulty === 'medium' ? __('dashboard.medium') : __('dashboard.hard')) }}
                                    </span>
                                    @if($todayTask->solution_id)
                                        <span class="flex items-center">
                                            <i class="fas fa-video mr-1"></i>
                                            {{ __('dashboard.video_guide') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center">
                                        <i class="fas fa-brain mr-1"></i>
                                        {{ $todayTask->type === 'mindfulness' ? __('dashboard.mindfulness') : 
                                           ($todayTask->type === 'physical' ? __('dashboard.physical') : __('dashboard.emotional')) }}
                                    </span>
                                </div>
                                
                                @if(!$todayTask->completed_at)
                                    <button onclick="completeTask({{ $todayTask->id }})" 
                                            class="emerald-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200 transform hover:scale-105 text-base sm:text-lg">
                                        <i class="fas fa-play mr-2"></i>
                                        {{ __('dashboard.start_practice') }}
                                    </button>
                                @else
                                    <div class="text-emerald-600 font-medium">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        {{ __('common.completed') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Order 2: My Virtue Tree -->
        <div class="order-2 md:order-2">
            <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8">
                <div class="text-center mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 spiritual-font mb-2">
                        {{ __('dashboard.my_virtue_tree') }}
                    </h2>
                    <p class="text-gray-600 text-sm">{{ __('dashboard.healing_journey') }}</p>
                </div>

                <!-- Tree Visual Representation -->
                <div class="flex flex-col items-center justify-center space-y-6 mb-6">
                    <!-- Tree Icon -->
                    <div class="relative">
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full flex items-center justify-center 
                                    {{ $treeStatus['health'] < 30 ? 'bg-gray-100' : 
                                       ($treeStatus['health'] >= 50 ? 'bg-emerald-100' : 'bg-yellow-100') }}">
                            <i class="fas fa-tree text-5xl sm:text-6xl 
                                      {{ $treeStatus['health'] < 30 ? 'text-gray-500' : 
                                         ($treeStatus['health'] >= 50 ? 'text-emerald-500' : 'text-yellow-500') }} 
                                      animate-float"></i>
                        </div>
                        
                        <!-- Season Overlay -->
                        @if($userTree->season === 'winter')
                            <div class="absolute top-0 right-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-snowflake text-blue-500 text-sm"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Tree Stats -->
                    <div class="text-center space-y-3 w-full">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                {{ $treeStatus['level'] }}
                            </h3>
                            <p class="text-gray-600 text-sm">{{ $treeStatus['message'] }}</p>
                        </div>

                        <div class="space-y-2">
                            <!-- Health Bar -->
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-heart text-red-500 text-sm"></i>
                                <div class="flex-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $treeStatus['health'] }}%"></div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $treeStatus['health'] }}%</span>
                            </div>
                            
                            <!-- Experience Bar -->
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <div class="flex-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ ($userTree->exp % 100) }}%"></div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $userTree->exp }} XP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Order 3: Charts & Balance Wheel -->
        <div class="md:col-span-2 lg:col-span-1 order-3 md:order-3">
            <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8">
                <div class="text-center mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 spiritual-font mb-2">
                        {{ __('dashboard.balance_wheel') }}
                    </h2>
                    <p class="text-gray-600 text-sm">{{ __('dashboard.inner_balance') }}</p>
                </div>

                <!-- Balance Wheel Visualization -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative w-48 h-48 sm:w-64 sm:h-64">
                        <!-- Simplified balance wheel representation -->
                        <div class="absolute inset-0 rounded-full border-4 border-emerald-200 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-yin-yang text-4xl md:text-5xl text-emerald-500 mb-2"></i>
                                <p class="text-sm text-gray-600">{{ __('dashboard.harmony') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Balance Stats -->
                    <div class="grid grid-cols-3 gap-4 text-center w-full">
                        <div>
                            <div class="text-2xl font-bold text-emerald-600">{{ __('dashboard.heart') }}</div>
                            <div class="text-sm text-gray-600">{{ __('dashboard.compassion') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-yellow-600">{{ __('dashboard.grit') }}</div>
                            <div class="text-sm text-gray-600">{{ __('dashboard.resilience') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ __('dashboard.wisdom') }}</div>
                            <div class="text-sm text-gray-600">{{ __('dashboard.clarity') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions - Full Width -->
        <div class="md:col-span-2 lg:col-span-3 order-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('meditate') }}" 
                   class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-spa text-3xl mb-3 animate-pulse-slow"></i>
                    <h3 class="font-bold text-lg mb-1">{{ __('navigation.meditation') }}</h3>
                    <p class="text-purple-100 text-sm">{{ __('dashboard.find_inner_peace') }}</p>
                </a>

                <button onclick="openDonateModal()" 
                        class="bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-heart text-3xl mb-3 animate-bounce-slow"></i>
                    <h3 class="font-bold text-lg mb-1">{{ __('dashboard.give_fruit') }}</h3>
                    <p class="text-pink-100 text-sm">{{ $userTree->fruits_balance }} {{ __('dashboard.available') }}</p>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
