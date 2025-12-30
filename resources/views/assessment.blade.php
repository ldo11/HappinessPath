@extends('layouts.guest')

@section('title', __('assessment.title'))
@section('auth-subtitle', __('assessment.subtitle'))

@section('content')
<div class="min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('home-bg.jpg') }}');">
    <div class="min-h-screen bg-gray-900/90">
        <div class="w-full px-4 sm:px-6 lg:px-8 mt-4 sm:mt-20">
            <div class="max-w-7xl mx-auto">
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl p-6 sm:p-10">
                    <!-- Assessment Header -->
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/20">
                            <i class="fas fa-brain text-emerald-300 text-2xl"></i>
                        </div>
                        <h1 class="text-white font-bold spiritual-font text-2xl sm:text-3xl mb-2">
                            {{ __('assessment.title') }}
                        </h1>
                        <p class="text-white/80 text-sm">
                            {{ __('assessment.description') }}
                        </p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between text-white/80 text-xs sm:text-sm mb-2">
                            <span>{{ __('assessment.progress') }}</span>
                            <span id="progressText">{{ __('assessment.question') }} 1 {{ __('assessment.of') }} 30</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-2.5 overflow-hidden">
                            <div id="progressBar" class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: 3.33%"></div>
                        </div>
                    </div>

                    <!-- Assessment Form -->
                    <form id="assessmentForm" class="space-y-6" method="POST" action="{{ route('assessment.submit') }}">
                        @csrf

                        <div id="questionContainer">
                            <!-- Questions will be dynamically loaded here -->
                            <div class="text-center text-white">
                                <i class="fas fa-spinner fa-spin text-3xl mb-4"></i>
                                <p class="text-white/80">{{ __('common.loading') }}...</p>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex items-center justify-between pt-2">
                            <button type="button" 
                                    id="prevBtn"
                                    onclick="previousQuestion()"
                                    class="bg-white/10 hover:bg-white/20 text-white px-5 py-3 rounded-xl transition-all duration-200 border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-arrow-left mr-2"></i>
                                {{ __('assessment.previous') }}
                            </button>

                            <div class="text-white/70 text-sm">
                                <span id="questionIndicator">1 / 30</span>
                            </div>

                            <button type="button" 
                                    id="nextBtn"
                                    onclick="nextQuestion()"
                                    class="bg-emerald-500/80 hover:bg-emerald-500 text-white px-5 py-3 rounded-xl transition-all duration-200 shadow-lg">
                                {{ __('assessment.next') }}
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>

                        <!-- Submit Button (hidden until all answered) -->
                        <div class="text-center hidden" id="submitSection">
                            <p class="text-white/80 mb-4 text-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ __('assessment.completed') }}
                            </p>
                            <button type="submit" 
                                    class="bg-emerald-500/80 hover:bg-emerald-500 text-white font-semibold py-4 px-8 rounded-xl text-lg transition-all duration-200 shadow-xl">
                                <i class="fas fa-chart-line mr-2"></i>
                                {{ __('assessment.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
const i18n = {
    'assessment.question': @json(__('assessment.question')),
    'assessment.of': @json(__('assessment.of')),
};

function __(key) {
    return i18n[key] ?? key;
}

// Assessment questions data
let questions = [
    {
        id: 1,
        question: "Bạn thường cảm thấy lo lắng về tương lai như thế nào?",
        type: "scale",
        options: [
            { value: 1, text: "Không bao giờ" },
            { value: 2, text: "Hiếm khi" },
            { value: 3, text: "Thỉnh thoảng" },
            { value: 4, text: "Thường xuyên" },
            { value: 5, text: "Luôn luôn" }
        ]
    },
    {
        id: 2,
        question: "Khi đối mặt với khó khăn, bạn thường:",
        type: "choice",
        options: [
            { value: 1, text: "Tìm kiếm sự giúp đỡ" },
            { value: 2, text: "Tự mình giải quyết" },
            { value: 3, text: "Tránh né vấn đề" },
            { value: 4, text: "Phân tích và lập kế hoạch" },
            { value: 5, text: "Nhận thức và chấp nhận" }
        ]
    },
    {
        id: 3,
        question: "Mức độ hài lòng với các mối quan hệ hiện tại của bạn:",
        type: "scale",
        options: [
            { value: 1, text: "Rất không hài lòng" },
            { value: 2, text: "Không hài lòng" },
            { value: 3, text: "Bình thường" },
            { value: 4, text: "Hài lòng" },
            { value: 5, text: "Rất hài lòng" }
        ]
    },
    {
        id: 4,
        question: "Bạn dành thời gian cho việc chăm sóc bản thân (ngủ, ăn uống, tập thể dục):",
        type: "scale",
        options: [
            { value: 1, text: "Rất ít" },
            { value: 2, text: "Ít" },
            { value: 3, text: "Đủ" },
            { value: 4, text: "Tốt" },
            { value: 5, text: "Rất tốt" }
        ]
    },
    {
        id: 5,
        question: "Khi cảm thấy căng thẳng, bạn thường:",
        type: "choice",
        options: [
            { value: 1, text: "Tập thể dục" },
            { value: 2, text: "Nói chuyện với bạn bè" },
            { value: 3, text: "Thiền định hoặc yoga" },
            { value: 4, text: "Đọc sách hoặc xem phim" },
            { value: 5, text: "Khóc hoặc thể hiện cảm xúc" }
        ]
    }
    // Add more questions to reach 30 total
];

// Generate remaining questions programmatically
for (let i = 6; i <= 30; i++) {
    const categories = ['cảm xúc', 'sức khỏe', 'mối quan hệ', 'công việc', 'tâm linh'];
    const category = categories[Math.floor(Math.random() * categories.length)];
    
    questions.push({
        id: i,
        question: `Câu hỏi ${i}: Bạn đánh giá như thế nào về ${category} của mình?`,
        type: "scale",
        options: [
            { value: 1, text: "Rất tệ" },
            { value: 2, text: "Tệ" },
            { value: 3, text: "Bình thường" },
            { value: 4, text: "Tốt" },
            { value: 5, text: "Rất tốt" }
        ]
    });
}

let currentQuestion = 0;
const answers = {};

function showQuestion(index) {
    const question = questions[index];
    const container = document.getElementById('questionContainer');
    
    let html = `
        <div class="space-y-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/20">
                    <span class="text-white font-semibold">${question.id}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-semibold leading-relaxed text-xl sm:text-3xl mb-6">
                        ${question.question}
                    </h3>

                    <div class="space-y-3 max-w-lg mx-auto">
    `;
    
    question.options.forEach(option => {
        const isSelected = answers[question.id] === option.value;
        const selectedClass = isSelected ? 'bg-emerald-500/80 ring-2 ring-emerald-300 border-emerald-300/40' : 'bg-white/10 hover:bg-white/20 border-white/20';
        const isChecked = isSelected ? 'checked' : '';
        html += `
            <label class="block w-full p-4 sm:p-5 rounded-2xl transition-all duration-200 cursor-pointer border ${selectedClass}">
                <input type="radio"
                       name="question_${question.id}"
                       value="${option.value}"
                       ${isChecked}
                       onchange="selectAnswer(${question.id}, ${option.value})"
                       class="sr-only">
                <div class="flex items-center justify-between">
                    <span class="text-white font-medium">${option.text}</span>
                    <span class="text-white/70 text-sm">${option.value}</span>
                </div>
            </label>
        `;
    });
    
    html += `
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Update progress
    updateProgress();
    updateButtons();
}

// Initialize question navigation
function initializeQuestionNav() {
    const navContainer = document.getElementById('questionNav');
    navContainer.innerHTML = '';
    
    questions.forEach((question, index) => {
        const questionNumber = index + 1;
        const isAnswered = answers[question.id] !== undefined;
        const isCurrent = index === currentQuestion;
        
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `
            w-8 h-8 rounded-full text-xs font-medium transition-all duration-200
            ${isAnswered 
                ? 'bg-emerald-500 text-white hover:bg-emerald-600' 
                : 'bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-400/30'}
            ${isCurrent ? 'ring-2 ring-emerald-400 ring-offset-2 ring-offset-slate-900' : ''}
        `;
        button.textContent = questionNumber;
        button.onclick = () => goToQuestion(index);
        
        navContainer.appendChild(button);
    });
}

// Go to specific question
function goToQuestion(index) {
    if (index >= 0 && index < questions.length) {
        currentQuestion = index;
        showQuestion(currentQuestion);
    }
}

function selectAnswer(questionId, value) {
    answers[questionId] = value;
    
    // Update navigation to reflect answered state
    initializeQuestionNav();

    // Update submit visibility immediately (important for last question)
    updateProgress();
    updateButtons();
    
    // Auto-advance to next question after selection
    setTimeout(() => {
        if (currentQuestion < questions.length - 1) {
            nextQuestion();
        }
    }, 300);
}

function updateProgress() {
    const progress = ((currentQuestion + 1) / questions.length) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = `${__('assessment.question')} ${currentQuestion + 1} ${__('assessment.of')} ${questions.length}`;
    document.getElementById('questionIndicator').textContent = `${currentQuestion + 1} / ${questions.length}`;
}

function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitSection = document.getElementById('submitSection');
    
    // Previous button
    if (currentQuestion === 0) {
        prevBtn.disabled = true;
        prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        prevBtn.disabled = false;
        prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    // Show submit button only when all questions are answered
    const allAnswered = Object.keys(answers).length === questions.length;
    
    if (allAnswered) {
        nextBtn.classList.add('hidden');
        submitSection.classList.remove('hidden');
    } else {
        nextBtn.classList.remove('hidden');
        submitSection.classList.add('hidden');
    }
}

function nextQuestion() {
    if (currentQuestion < questions.length - 1) {
        currentQuestion++;
        showQuestion(currentQuestion);
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        currentQuestion--;
        showQuestion(currentQuestion);
    }
}

// Initialize assessment when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load questions from server
    fetch('/api/assessment/questions')
        .then(response => response.json())
        .then(data => {
            questions = data;
            initializeQuestionNav();
            showQuestion(0);
        })
        .catch(error => {
            console.error('Error loading questions:', error);
            // Fallback to hardcoded questions if API fails
            initializeQuestionNav();
            showQuestion(0);
        });
});
document.getElementById('assessmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate all questions are answered
    if (Object.keys(answers).length < questions.length) {
        showNotification('Vui lòng trả lời tất cả câu hỏi', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang phân tích...';
    
    // Create hidden inputs for answers
    Object.keys(answers).forEach(questionId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `answers[${questionId}]`;
        input.value = answers[questionId];
        e.target.appendChild(input);
    });
    
    // Submit form
    e.target.submit();
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm glassmorphism ${
        type === 'success' ? 'text-emerald-200' : 'text-red-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
@endsection
