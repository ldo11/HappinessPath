@extends('layouts.app')

@section('title', 'Meditation - Your Happiness Path')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-green-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Meditation</h1>
                    <p class="text-sm text-gray-600">Find your inner peace</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-fire text-orange-500"></i>
                            <span class="text-sm font-medium">{{ $streakInfo['current_streak'] }} day streak</span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $treeStatus['message'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-6">
        @if(!session('meditation_session.started'))
            <!-- Meditation Type Selection -->
            <div id="typeSelection" class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Choose Your Meditation</h2>
                
                @foreach($meditationTypes as $type)
                    <div class="bg-white rounded-2xl shadow-lg p-6 cursor-pointer hover:shadow-xl transition-all duration-200"
                         onclick="selectMeditationType('{{ $type['id'] }}')">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-{{ $type['color'] }}-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas {{ $type['icon'] }} text-{{ $type['color'] }}-600 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $type['name'] }}</h3>
                                <p class="text-gray-600 text-sm">{{ $type['description'] }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    Available durations: {{ implode(', ', array_map(fn($d) => $d . ' min', $type['duration'])) }}
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Duration Selection (Hidden initially) -->
            <div id="durationSelection" class="hidden">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <button onclick="backToTypeSelection()" class="mr-4 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Select Duration</h2>
                            <p class="text-sm text-gray-600" id="selectedTypeName">Mindfulness</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6" id="durationOptions">
                        <!-- Duration options will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        @else
            <!-- Active Meditation Session -->
            <div id="activeSession" class="flex flex-col items-center justify-center py-8">
                <!-- Timer Circle -->
                <div class="relative w-64 h-64 mb-8">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="128" cy="128" r="120" stroke="#e5e7eb" stroke-width="8" fill="none" />
                        <circle id="progressCircle" 
                                cx="128" cy="128" r="120" 
                                stroke="#8b5cf6" 
                                stroke-width="8" 
                                fill="none"
                                stroke-linecap="round"
                                class="timer-circle"
                                style="stroke-dasharray: 754; stroke-dashoffset: 754;" />
                    </svg>
                    
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div id="timerDisplay" class="text-5xl font-bold text-gray-900">00:00</div>
                        <div id="sessionType" class="text-sm text-gray-600 mt-2">Mindfulness</div>
                        <div class="flex items-center mt-4 space-x-4">
                            <button onclick="togglePlayPause()" id="playPauseBtn"
                                    class="w-12 h-12 bg-purple-600 hover:bg-purple-700 text-white rounded-full flex items-center justify-center transition-colors">
                                <i class="fas fa-pause text-lg"></i>
                            </button>
                            <button onclick="stopMeditation()" 
                                    class="w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center transition-colors">
                                <i class="fas fa-stop text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Audio Controls -->
                <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Guided Audio</h3>
                        <button onclick="toggleAudio()" id="audioToggle"
                                class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-volume-up text-xl"></i>
                        </button>
                    </div>
                    
                    <audio id="meditationAudio" loop>
                        <source src="/audio/mindfulness.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="skipAudio(-10)" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-backward"></i>
                        </button>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div id="audioProgress" class="bg-purple-600 h-2 rounded-full transition-all duration-200" style="width: 0%"></div>
                        </div>
                        <button onclick="skipAudio(10)" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-forward"></i>
                        </button>
                    </div>
                </div>

                <!-- Session Tips -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 w-full max-w-md mt-6">
                    <h4 class="font-semibold text-blue-900 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i>Meditation Tips
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Find a comfortable, quiet position</li>
                        <li>• Focus on your breath, sensations, or guided voice</li>
                        <li>• It's normal for your mind to wander - gently return focus</li>
                        <li>• Be kind to yourself throughout the practice</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Session Complete Modal -->
<div id="sessionCompleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Session Complete!</h3>
        <p id="sessionResultMessage" class="text-gray-600 mb-6">Great job! You've completed your meditation.</p>
        
        <div id="sessionResults" class="space-y-3 mb-6">
            <!-- Results will be populated by JavaScript -->
        </div>
        
        <button onclick="closeSessionCompleteModal()" 
                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
            Continue
        </button>
    </div>
</div>

@section('scripts')
<script>
let meditationTimer = null;
let sessionStartTime = null;
let selectedType = null;
let selectedDuration = null;
let isPaused = false;
let pausedTime = 0;

const meditationTypes = @json($meditationTypes);

function selectMeditationType(typeId) {
    selectedType = meditationTypes.find(t => t.id === typeId);
    if (!selectedType) return;
    
    document.getElementById('typeSelection').classList.add('hidden');
    document.getElementById('durationSelection').classList.remove('hidden');
    document.getElementById('selectedTypeName').textContent = selectedType.name;
    
    // Populate duration options
    const durationOptions = document.getElementById('durationOptions');
    durationOptions.innerHTML = '';
    
    selectedType.duration.forEach(duration => {
        const button = document.createElement('button');
        button.className = 'bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200';
        button.innerHTML = `
            <div class="text-2xl font-bold text-gray-900">${duration}</div>
            <div class="text-sm text-gray-600">minutes</div>
        `;
        button.onclick = () => startMeditation(duration);
        durationOptions.appendChild(button);
    });
}

function backToTypeSelection() {
    document.getElementById('durationSelection').classList.add('hidden');
    document.getElementById('typeSelection').classList.remove('hidden');
}

async function startMeditation(duration) {
    selectedDuration = duration;
    
    try {
        const response = await fetch('{{ route("meditation.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type: selectedType.id,
                duration: duration
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show active session UI
            document.getElementById('durationSelection').classList.add('hidden');
            document.getElementById('activeSession').classList.remove('hidden');
            
            // Update session info
            document.getElementById('sessionType').textContent = selectedType.name;
            
            // Set up audio
            const audio = document.getElementById('meditationAudio');
            audio.src = selectedType.audio_url;
            
            // Start timer
            startTimer(duration * 60); // Convert to seconds
            
            // Auto-play audio
            audio.play().catch(e => console.log('Audio autoplay prevented:', e));
        }
    } catch (error) {
        console.error('Error starting meditation:', error);
        alert('Failed to start meditation session');
    }
}

function startTimer(durationSeconds) {
    sessionStartTime = Date.now();
    let remainingTime = durationSeconds;
    
    updateTimerDisplay(remainingTime);
    
    meditationTimer = setInterval(() => {
        if (!isPaused) {
            remainingTime--;
            updateTimerDisplay(remainingTime);
            updateProgressCircle(remainingTime, durationSeconds);
            
            if (remainingTime <= 0) {
                completeMeditation();
            }
        }
    }, 1000);
}

function updateTimerDisplay(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    document.getElementById('timerDisplay').textContent = 
        `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function updateProgressCircle(remaining, total) {
    const circle = document.getElementById('progressCircle');
    const circumference = 2 * Math.PI * 120;
    const progress = (total - remaining) / total;
    const offset = circumference - (progress * circumference);
    circle.style.strokeDashoffset = offset;
}

function togglePlayPause() {
    isPaused = !isPaused;
    const btn = document.getElementById('playPauseBtn');
    const audio = document.getElementById('meditationAudio');
    
    if (isPaused) {
        btn.innerHTML = '<i class="fas fa-play text-lg"></i>';
        audio.pause();
    } else {
        btn.innerHTML = '<i class="fas fa-pause text-lg"></i>';
        audio.play();
    }
}

function stopMeditation() {
    if (meditationTimer) {
        clearInterval(meditationTimer);
    }
    
    completeMeditation();
}

async function completeMeditation() {
    if (meditationTimer) {
        clearInterval(meditationTimer);
    }
    
    // Stop audio
    const audio = document.getElementById('meditationAudio');
    audio.pause();
    audio.currentTime = 0;
    
    try {
        const response = await fetch('{{ route("meditation.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSessionComplete(data);
        } else {
            alert(data.message || 'Failed to complete session');
        }
    } catch (error) {
        console.error('Error completing meditation:', error);
        alert('Failed to complete session');
    }
}

function showSessionComplete(results) {
    const modal = document.getElementById('sessionCompleteModal');
    const message = document.getElementById('sessionResultMessage');
    const resultsDiv = document.getElementById('sessionResults');
    
    message.textContent = results.message;
    
    resultsDiv.innerHTML = `
        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
            <span class="text-green-800">EXP Earned</span>
            <span class="font-bold text-green-900">+${results.exp_gained}</span>
        </div>
        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
            <span class="text-blue-800">Tree Health</span>
            <span class="font-bold text-blue-900">+${results.health_improved}%</span>
        </div>
        ${results.leveled_up ? `
            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-purple-800">Level Up!</span>
                <span class="font-bold text-purple-900">${results.previous_level} → ${results.new_level}</span>
            </div>
        ` : ''}
        ${results.fruits_earned > 0 ? `
            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                <span class="text-yellow-800">Fruits Earned</span>
                <span class="font-bold text-yellow-900">+${results.fruits_earned}</span>
            </div>
        ` : ''}
    `;
    
    modal.classList.remove('hidden');
}

function closeSessionCompleteModal() {
    document.getElementById('sessionCompleteModal').classList.add('hidden');
    window.location.href = '{{ route("dashboard") }}';
}

function toggleAudio() {
    const audio = document.getElementById('meditationAudio');
    const btn = document.getElementById('audioToggle');
    
    if (audio.muted) {
        audio.muted = false;
        btn.innerHTML = '<i class="fas fa-volume-up text-xl"></i>';
    } else {
        audio.muted = true;
        btn.innerHTML = '<i class="fas fa-volume-mute text-xl"></i>';
    }
}

function skipAudio(seconds) {
    const audio = document.getElementById('meditationAudio');
    audio.currentTime = Math.max(0, Math.min(audio.duration, audio.currentTime + seconds));
}

// Update audio progress bar
document.getElementById('meditationAudio').addEventListener('timeupdate', function() {
    const progress = (this.currentTime / this.duration) * 100;
    document.getElementById('audioProgress').style.width = progress + '%';
});

// Handle page visibility for pause/resume
document.addEventListener('visibilitychange', function() {
    if (document.hidden && !isPaused && meditationTimer) {
        togglePlayPause();
    }
});

// Handle back button during active session
window.addEventListener('popstate', function(event) {
    if (meditationTimer) {
        event.preventDefault();
        if (confirm('Are you sure you want to leave your meditation session?')) {
            stopMeditation();
        }
    }
});
</script>
@endsection
@endsection
