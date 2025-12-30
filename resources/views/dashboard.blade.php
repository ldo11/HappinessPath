@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('content')
<div class="min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div class="text-gray-100">
            <div class="text-lg font-semibold">{{ Auth::user()->name }}</div>
        </div>
        @if(!Auth::user()->email_verified_at)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                Chưa xác thực
            </span>
        @endif
    </div>
    <!-- Daily Task Choices (Body / Mind / Wisdom) -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('videos.index', ['pillar' => 'body']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-running text-emerald-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Body</h3>
                        <p class="text-sm text-gray-600">Bài tập cho cơ thể để tăng năng lượng và giảm căng thẳng.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('videos.index', ['pillar' => 'mind']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-brain text-emerald-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Mind</h3>
                        <p class="text-sm text-gray-600">Thực hành chánh niệm để an trú và quan sát cảm xúc.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('videos.index', ['pillar' => 'wisdom']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-compass text-emerald-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Wisdom</h3>
                        <p class="text-sm text-gray-600">Gợi ý giúp bạn tìm định hướng và phát triển trí tuệ.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if($hasQuizResult && $topPainPoints->count() > 0)
        <!-- Pain Points (Top 3) -->
        <div class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 spiritual-font">Nỗi khổ</h2>
                    <p class="text-sm text-gray-600">Top 3 nỗi khổ theo mức độ hiện tại của bạn.</p>
                </div>
                <a href="{{ route('pain-points.index') }}" class="bg-gray-900 text-white px-4 py-2.5 rounded-lg shadow-sm hover:shadow-md transition text-center">
                    Quản lý tất cả vấn đề
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topPainPoints as $painPoint)
                    @php
                        $selectedSeverity = (int) ($userPainPoints[$painPoint->id] ?? 0);
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $painPoint->name }}</div>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, max(0, $selectedSeverity * 10)) }}%"></div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">Mức độ: {{ $selectedSeverity }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Responsive Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column (Task) -->
        <div class="lg:col-span-8">
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

                                @php
                                    $taskId = (int) ($todayTask->id ?? 0);
                                    $canStartMission = $taskId > 0;
                                @endphp

                                @if($dailyMissionCompleted)
                                    <div class="text-emerald-600 font-medium" id="dailyMissionCompletedState">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Bạn đã hoàn thành nhiệm vụ hôm nay. Hãy quay lại vào ngày mai!
                                    </div>
                                @else
                                    <div class="flex items-center gap-3">
                                        <button type="button" id="startMissionBtn"
                                                class="emerald-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200 transform hover:scale-105 text-base sm:text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled(! $canStartMission)>
                                            <i class="fas fa-play mr-2"></i>
                                            Bắt đầu thực hiện
                                        </button>

                                        <div id="missionTimer" class="hidden px-4 py-2 rounded-lg bg-white/70 border border-white/40 text-gray-800 font-semibold">
                                            15:00
                                        </div>
                                    </div>

                                    @if(! $canStartMission)
                                        <div class="mt-3 text-sm text-gray-600">Nhiệm vụ hôm nay chưa sẵn sàng. Vui lòng thử lại sau.</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Tree + Wheel) -->
        <div class="lg:col-span-4 space-y-8">
            <div id="my-tree" class="glass-card rounded-2xl shadow-xl p-6 md:p-8">
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
                            <i class="fas {{ $treeStatus['icon'] ?? 'fa-tree' }} text-5xl sm:text-6xl 
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
    </div>

    <div id="dailyMissionReportModal" class="hidden fixed inset-0 z-50">
        <div id="dailyMissionReportBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Báo cáo kết quả</h3>
                        <p class="text-sm text-gray-600">Bạn đã trải nghiệm bài tập này thế nào?</p>
                    </div>
                    <button type="button" id="closeDailyMissionModal" class="text-gray-500 hover:text-gray-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <textarea id="dailyMissionReportContent" rows="5" class="w-full rounded-xl border border-gray-200 p-4 focus:outline-none focus:ring-2 focus:ring-emerald-300" placeholder="Viết cảm nhận của bạn..." required></textarea>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" id="submitDailyMissionReport" class="bg-gray-900 text-white px-5 py-3 rounded-lg">Hoàn thành nhiệm vụ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
(function () {
    const taskId = @json((int) ($todayTask->id ?? 0));
    const isCompletedInitially = @json((bool) ($dailyMissionCompleted ?? false));
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
        el.className = `fixed top-20 right-4 z-50 glassmorphism rounded-lg px-5 py-3 text-white max-w-sm ${type === 'success' ? '' : 'text-red-200'}`;
        el.innerHTML = `<div class="flex items-center gap-2"><i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-circle text-red-400'}"></i><span>${message}</span></div>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    function openModal() {
        modal.classList.remove('hidden');
        reportInput.focus();
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
        if (timerEl) timerEl.classList.add('hidden');

        const container = document.getElementById('dailyMissionCompletedState');
        if (container) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.id = 'dailyMissionCompletedState';
        wrapper.className = 'text-emerald-600 font-medium';
        wrapper.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Bạn đã hoàn thành nhiệm vụ hôm nay. Hãy quay lại vào ngày mai!';

        const parent = timerEl ? timerEl.parentElement : null;
        if (parent) {
            parent.appendChild(wrapper);
        }

        const xpText = document.querySelector('.fa-star')?.closest('.flex')?.querySelector('span.text-xs.text-gray-600');
        if (xpText && typeof newXp === 'number') {
            xpText.textContent = `${newXp} XP`;
        }
    }

    function startTimer(startEpochMs) {
        if (!startBtn || !timerEl) return;
        startBtn.classList.add('hidden');
        timerEl.classList.remove('hidden');

        stopTimer();

        function tick() {
            const elapsed = Math.floor((Date.now() - startEpochMs) / 1000);
            const remaining = Math.max(0, DURATION_SECONDS - elapsed);
            timerEl.textContent = formatTime(remaining);

            if (remaining <= 0) {
                stopTimer();
                openModal();
            }
        }

        tick();
        intervalId = setInterval(tick, 1000);
    }

    if (!isCompletedInitially && taskId > 0) {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            const startEpochMs = parseInt(stored, 10);
            if (!Number.isNaN(startEpochMs)) {
                const elapsed = Math.floor((Date.now() - startEpochMs) / 1000);
                if (elapsed < DURATION_SECONDS) {
                    startTimer(startEpochMs);
                } else {
                    openModal();
                }
            }
        }
    }

    if (startBtn) {
        startBtn.addEventListener('click', function () {
            if (taskId <= 0) return;
            const startEpochMs = Date.now();
            localStorage.setItem(STORAGE_KEY, String(startEpochMs));
            startTimer(startEpochMs);
        });
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    if (submitBtn) {
        submitBtn.addEventListener('click', async function () {
            if (taskId <= 0) return;
            const content = (reportInput.value || '').trim();
            if (!content) {
                toast('Vui lòng nhập nội dung báo cáo.', 'error');
                return;
            }

            submitBtn.disabled = true;

            try {
                const res = await fetch(@json(route('daily-mission.complete')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ task_id: taskId, report_content: content }),
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    toast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                    submitBtn.disabled = false;
                    return;
                }

                closeModal();
                setCompletedUI(data.new_exp);
                toast(`Bạn nhận được +${data.xp_awarded} XP!`, 'success');
            } catch (e) {
                toast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                submitBtn.disabled = false;
            }
        });
    }
})();
</script>
@endsection
