@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('content')
<div class="min-h-screen space-y-8">
    
    <!-- Profile & Soul Stats Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Summary (Col 1) -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-full flex items-center justify-center text-2xl">
                    <i class="fas fa-user-circle text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-emerald-600 font-medium">{{ Auth::user()->nickname ?? 'Wellness Seeker' }}</p>
                </div>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-600 italic text-sm">
                    "{{ Auth::user()->introduction ?? 'Every step is a journey...' }}"
                </p>
            </div>

            <div class="flex items-center justify-between text-sm">
                <a href="{{ route('user.profile.settings.edit') }}" class="text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                    <i class="fas fa-edit"></i> {{ __('dashboard.edit_profile') }}
                </a>
                @if(!Auth::user()->email_verified_at)
                    <span class="text-amber-600 bg-amber-50 px-2 py-1 rounded-md">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('dashboard.verify_email') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Soul Stats (Col 2 & 3) -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Body Stat -->
            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-running text-blue-500 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ __('dashboard.body') }}</h3>
                            <div class="text-xs text-gray-500">Lv. {{ $levels['body'] }}</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-blue-600">{{ $user->xp_body }}</span>
                </div>
                <div>
                    @if($user->xp_body == 0)
                        <div class="text-center">
                            <a href="{{ route('videos.index', ['pillar' => 'body']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                {{ __('dashboard.start_journey') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>XP</span>
                            <span>{{ $progress['body'] }}/100</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress['body'] }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mind Stat -->
            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center">
                            <i class="fas fa-heart text-pink-500 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ __('dashboard.mind') }}</h3>
                            <div class="text-xs text-gray-500">Lv. {{ $levels['mind'] }}</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-pink-600">{{ $user->xp_mind }}</span>
                </div>
                <div>
                    @if($user->xp_mind == 0)
                        <div class="text-center">
                            <a href="{{ route('videos.index', ['pillar' => 'mind']) }}" class="text-xs font-bold text-pink-600 hover:text-pink-700 hover:underline">
                                {{ __('dashboard.start_journey') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>XP</span>
                            <span>{{ $progress['mind'] }}/100</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-pink-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress['mind'] }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Wisdom Stat -->
            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                            <i class="fas fa-lightbulb text-yellow-500 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ __('dashboard.wisdom') }}</h3>
                            <div class="text-xs text-gray-500">Lv. {{ $levels['wisdom'] }}</div>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-yellow-600">{{ $user->xp_wisdom }}</span>
                </div>
                <div>
                    @if($user->xp_wisdom == 0)
                        <div class="text-center">
                            <a href="{{ route('videos.index', ['pillar' => 'wisdom']) }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700 hover:underline">
                                {{ __('dashboard.start_journey') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>XP</span>
                            <span>{{ $progress['wisdom'] }}/100</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress['wisdom'] }}%"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content: Today's Practice (Left) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Today's Practice Card -->
            <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-100 rounded-full -mr-16 -mt-16 opacity-20"></div>
                <div class="flex items-center justify-between mb-6 relative">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 spiritual-font mb-1">
                            @if(isset($missionSet) && $missionSet)
                                {{ $missionSet->name }}
                            @else
                                {{ __('dashboard.today_practice') }}
                            @endif
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

                <!-- Task Details -->
                <div class="bg-gradient-to-br from-emerald-50 to-blue-50 rounded-xl p-6 border border-emerald-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-200">
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
                                <div class="bg-white/70 rounded-lg p-4 mb-4 border border-white">
                                    <h4 class="font-medium text-gray-800 mb-2 flex items-center gap-2">
                                        <i class="fas fa-list-ul text-emerald-500 text-xs"></i> {{ __('dashboard.instructions') }}
                                    </h4>
                                    <ul class="space-y-1 text-sm text-gray-600">
                                        @foreach($instructionsList as $instruction)
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-emerald-500 mr-2 mt-0.5 text-xs"></i>
                                                <span>{{ $instruction }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 bg-white rounded-md border border-gray-100 shadow-sm">
                                        <i class="fas fa-signal mr-1 text-gray-400"></i>
                                        {{ $todayTask->difficulty === 'easy' ? __('dashboard.easy') : 
                                           ($todayTask->difficulty === 'medium' ? __('dashboard.medium') : __('dashboard.hard')) }}
                                    </span>
                                    @if(isset($todayTask->points))
                                        <span class="px-2 py-1 bg-white rounded-md border border-gray-100 shadow-sm">
                                            <i class="fas fa-star mr-1 text-gray-400"></i>
                                            {{ $todayTask->points }} {{ __('dashboard.points') }}
                                        </span>
                                    @endif
                                    @if($todayTask->solution_id)
                                        <span class="px-2 py-1 bg-white rounded-md border border-gray-100 shadow-sm">
                                            <i class="fas fa-video mr-1 text-gray-400"></i>
                                            {{ __('dashboard.video_guide') }}
                                        </span>
                                    @endif
                                </div>

                                @php
                                    $taskId = $todayTask->id ?? 0;
                                    $isProgramCompleted = $todayTask->is_completed_program ?? false;
                                    $hasMissionSet = isset($missionSet) && $missionSet;
                                    $canStartMission = !empty($taskId) && !$isProgramCompleted; 
                                @endphp

                                @if($isProgramCompleted)
                                    <div class="text-emerald-600 font-bold bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100">
                                        <i class="fas fa-trophy mr-2"></i>
                                        {{ __('dashboard.program_completed_title') }}
                                    </div>
                                @elseif(!$hasMissionSet)
                                    <a href="{{ route('user.mission-sets.index', ['locale' => app()->getLocale()]) }}" class="emerald-gradient text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-medium inline-flex items-center">
                                        <i class="fas fa-compass mr-2"></i>
                                        {{ __('dashboard.browse_programs') }}
                                    </a>
                                @elseif($dailyMissionCompleted)
                                    <div class="text-emerald-600 font-bold bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100" id="dailyMissionCompletedState">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        {{ __('dashboard.mission_completed_message') }}
                                    </div>
                                @else
                                    <div class="flex items-center gap-3">
                                        <button type="button" id="startMissionBtn"
                                                class="emerald-gradient text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled(! $canStartMission)>
                                            <i class="fas fa-play mr-2"></i>
                                            {{ __('dashboard.start_mission') }}
                                        </button>

                                        <div id="missionTimer" class="hidden px-4 py-2 rounded-lg bg-white border border-emerald-200 text-emerald-700 font-mono font-bold shadow-inner">
                                            15:00
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Resources / Legacy Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('videos.index', ['pillar' => 'body']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100 group">
                    <div class="flex flex-col gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
                            <i class="fas fa-running text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ __('dashboard.explore_body') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.exercises_energy') }}</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('videos.index', ['pillar' => 'mind']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100 group">
                    <div class="flex flex-col gap-3">
                        <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center group-hover:bg-pink-100 transition">
                            <i class="fas fa-brain text-pink-500"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ __('dashboard.explore_mind') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.mindfulness_peace') }}</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('videos.index', ['pillar' => 'wisdom']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 border border-gray-100 group">
                    <div class="flex flex-col gap-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center group-hover:bg-yellow-100 transition">
                            <i class="fas fa-compass text-yellow-500"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ __('dashboard.explore_wisdom') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.growth_direction') }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Right Sidebar: Pain Points & Status -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Pain Points Matrix -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">{{ __('dashboard.my_pain_points') }}</h3>
                    <a href="{{ route('user.pain-points.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">{{ __('dashboard.manage') }}</a>
                </div>
                
                @if($myPainPoints->count() > 0)
                    <div class="divide-y divide-gray-50">
                        @foreach($myPainPoints->take(5) as $pp)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full {{ $pp->pivot->score > 7 ? 'bg-red-500' : ($pp->pivot->score > 4 ? 'bg-orange-500' : 'bg-green-500') }}"></div>
                                    <span class="text-sm font-medium text-gray-800">{{ $pp->getTranslatedName() }}</span>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100 text-gray-600">{{ $pp->pivot->score }}/10</span>
                            </div>
                        @endforeach
                        
                        @if($myPainPoints->count() > 5)
                            <div class="p-3 text-center">
                                <a href="{{ route('user.pain-points.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">
                                    + {{ $myPainPoints->count() - 5 }} more
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 bg-gray-50">
                        <a href="{{ route('user.pain-points.index') }}" class="block w-full text-center py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                            {{ __('dashboard.update_pain_points') }}
                        </a>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-feather text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">{{ __('dashboard.no_pain_points') }}</p>
                        <a href="{{ route('user.pain-points.index') }}" class="inline-block px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition">
                            {{ __('dashboard.start_tracking') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Links / Status -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-5 text-white">
                <h3 class="font-bold text-lg mb-2">{{ __('dashboard.need_guidance') }}</h3>
                <p class="text-indigo-100 text-sm mb-4">{{ __('dashboard.connect_consultant') }}</p>
                <a href="{{ route('user.consultations.create') }}" class="block w-full text-center py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg text-sm font-medium transition border border-white/30">
                    {{ __('dashboard.book_consultation') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Daily Mission Modal -->
    <div id="dailyMissionReportModal" class="hidden fixed inset-0 z-50">
        <div id="dailyMissionReportBackdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-0 overflow-hidden transform transition-all scale-100">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('dashboard.report_title') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('dashboard.report_subtitle') }}</p>
                    </div>
                    <button type="button" id="closeDailyMissionModal" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 text-gray-500 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <textarea id="dailyMissionReportContent" rows="5" class="w-full rounded-xl border border-gray-200 p-4 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 transition resize-none" placeholder="{{ __('dashboard.report_placeholder') }}" required></textarea>
                    
                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-gray-500"><i class="fas fa-shield-alt mr-1"></i> Your reflection is private</p>
                        <button type="button" id="submitDailyMissionReport" class="bg-gray-900 text-white px-6 py-3 rounded-xl hover:bg-gray-800 transition shadow-lg shadow-gray-200 font-medium">
                            {{ __('dashboard.complete_mission') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const taskId = @json($todayTask->id ?? 0);
    const isCompletedInitially = @json((bool) ($dailyMissionCompleted ?? false));
    const isFallbackTask = typeof taskId === 'string' && taskId.startsWith('fallback_');
    const startBtn = document.getElementById('startMissionBtn');
    const timerEl = document.getElementById('missionTimer');
    const modal = document.getElementById('dailyMissionReportModal');
    const backdrop = document.getElementById('dailyMissionReportBackdrop');
    const closeModalBtn = document.getElementById('closeDailyMissionModal');
    const submitBtn = document.getElementById('submitDailyMissionReport');
    const reportInput = document.getElementById('dailyMissionReportContent');

    const STORAGE_KEY = `daily_mission_start_${taskId}`;
    const DURATION_SECONDS = 15 * 60;

    function toast(message, type) {
        const el = document.createElement('div');
        el.className = `fixed top-20 right-4 z-50 bg-white/90 backdrop-blur shadow-xl border-l-4 rounded-lg px-6 py-4 text-gray-800 max-w-sm transform transition-all duration-300 translate-x-full ${type === 'success' ? 'border-emerald-500' : 'border-red-500'}`;
        el.innerHTML = `<div class="flex items-center gap-3"><div class="rounded-full w-6 h-6 flex items-center justify-center ${type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'}"><i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation'} text-xs"></i></div><span class="font-medium">${message}</span></div>`;
        document.body.appendChild(el);
        
        requestAnimationFrame(() => {
            el.classList.remove('translate-x-full');
        });

        setTimeout(() => {
            el.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        }, 3000);
    }

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            if(reportInput) reportInput.focus();
        }, 100);
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    let intervalId = null;

    function stopTimer() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    function setCompletedUI(newXp) {
        stopTimer();
        localStorage.removeItem(STORAGE_KEY);

        if (startBtn) startBtn.classList.add('hidden');
        if (timerEl) timerEl.parentElement.classList.add('hidden'); // Hide container

        const container = document.getElementById('dailyMissionCompletedState');
        if (container) {
            return;
        }
        
        // Find where to insert completed state
        // In new layout, it replaces the start button group
        const btnGroup = document.getElementById('startMissionBtn')?.parentElement;
        if(btnGroup) {
            btnGroup.innerHTML = `<div class="text-emerald-600 font-bold bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100"><i class="fas fa-check-circle mr-2"></i>{{ __('dashboard.mission_completed_message') }}</div>`;
        }
    }

    function startTimer(startEpochMs) {
        if (!startBtn || !timerEl) return;
        startBtn.classList.add('hidden');
        timerEl.classList.remove('hidden');
        
        // Also ensure timer container is visible if hidden
        if(timerEl.parentElement.classList.contains('hidden')) {
            timerEl.parentElement.classList.remove('hidden');
        }

        stopTimer();

        function tick() {
            const elapsed = Math.floor((Date.now() - startEpochMs) / 1000);
            const remaining = Math.max(0, DURATION_SECONDS - elapsed);
            timerEl.textContent = formatTime(remaining);

            if (remaining <= 0) {
                stopTimer();
                if (isFallbackTask) {
                    setCompletedUI(0);
                    toast('{{ __('dashboard.mission_completed_message') }}', 'success');
                } else {
                    openModal();
                }
            }
        }

        tick();
        intervalId = setInterval(tick, 1000);
    }

    if (!isCompletedInitially && taskId) {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            const startEpochMs = parseInt(stored, 10);
            if (!Number.isNaN(startEpochMs)) {
                const elapsed = Math.floor((Date.now() - startEpochMs) / 1000);
                if (elapsed < DURATION_SECONDS) {
                    startTimer(startEpochMs);
                } else {
                    if (isFallbackTask) {
                        setCompletedUI(0);
                    } else {
                        openModal();
                    }
                }
            }
        }
    }

    if (startBtn) {
        startBtn.addEventListener('click', function () {
            if (!taskId) return;
            const startEpochMs = Date.now();
            localStorage.setItem(STORAGE_KEY, String(startEpochMs));
            startTimer(startEpochMs);
        });
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    if (submitBtn) {
        submitBtn.addEventListener('click', async function () {
            if (!taskId || isFallbackTask) return;
            const content = (reportInput.value || '').trim();
            if (!content) {
                toast('{{ __('dashboard.report_required') }}', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

            try {
                const res = await fetch(@json(route('daily-mission.complete')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ task_id: taskId, report_content: content }),
                });

                const data = await res.json();
                if (!res.ok || (data.success === false && !data.already_completed)) {
                    toast(data.message || '{{ __('dashboard.error_occurred') }}', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '{{ __('dashboard.complete_mission') }}';
                    return;
                }

                closeModal();
                setCompletedUI(data.new_exp);
                toast(`{{ __('dashboard.xp_awarded_message', ['xp' => '+${data.xp_awarded}']) }}`, 'success');
            } catch (e) {
                console.error(e);
                toast('{{ __('dashboard.error_occurred') }}', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __('dashboard.complete_mission') }}';
            }
        });
    }
})();
</script>
@endsection
