@extends('layouts.guest')

@section('title', __('assessment.title'))
@section('auth-subtitle', __('assessment.subtitle'))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Assessment Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-emerald-600/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-float border border-emerald-400/30">
            <i class="fas fa-brain text-emerald-400 text-3xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-white spiritual-font mb-2">
            {{ __('assessment.title') }}
        </h1>
        <p class="text-emerald-200 text-sm">
            {{ __('assessment.description') }}
        </p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between text-emerald-200 text-sm mb-2">
            <span>{{ __('assessment.progress') }}</span>
            <span id="progressText">{{ __('assessment.question') }} 1 {{ __('assessment.of') }} 30</span>
        </div>
        <div class="w-full bg-emerald-900/30 rounded-full h-3">
            <div id="progressBar" class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: 3.33%"></div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form id="assessmentForm" class="space-y-6" method="POST" action="{{ route('assessment.submit') }}">
        @csrf
        
        <div id="questionContainer" class="glassmorphism rounded-2xl p-8">
            <!-- Questions will be dynamically loaded here -->
            <div class="text-center text-white">
                <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
                <p>{{ __('common.loading') }}...</p>
            </div>
        </div>

        <!-- Question Navigation -->
        <div class="mt-6">
            <div class="glassmorphism rounded-xl p-4">
                <h3 class="text-emerald-200 text-sm font-medium mb-3 text-center">{{ __('assessment.progress') }}</h3>
                <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2" id="questionNav">
                    <!-- Question numbers will be dynamically added here -->
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex items-center justify-between mt-8">
            <button type="button" 
                    id="prevBtn"
                    onclick="previousQuestion()"
                    class="bg-emerald-600/20 text-emerald-300 px-6 py-3 rounded-lg transition-all duration-200 border border-emerald-400/30 hover:bg-emerald-600/30 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('assessment.previous') }}
            </button>

            <div class="text-emerald-200 text-sm">
                <span id="questionIndicator">1 / 30</span>
            </div>

            <button type="button" 
                    id="nextBtn"
                    onclick="nextQuestion()"
                    class="emerald-gradient text-white px-6 py-3 rounded-lg transition-all duration-200 shadow-lg">
                {{ __('assessment.next') }}
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>

        <!-- Submit Button (hidden until last question) -->
        <div class="text-center hidden" id="submitSection">
            <p class="text-emerald-200 mb-4 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ __('assessment.completed') }}
            </p>
            <button type="submit" 
                    class="emerald-gradient text-white font-semibold py-4 px-8 rounded-lg text-lg transition-all duration-200 transform hover:scale-[1.02] shadow-xl">
                <i class="fas fa-chart-line mr-2"></i>
                {{ __('assessment.submit') }}
            </button>
        </div>
    </form>
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
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">${question.id}</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-white mb-6 leading-relaxed">
                        ${question.question}
                    </h3>
                    
                    <div class="space-y-3">
    `;
    
    question.options.forEach(option => {
        const isChecked = answers[question.id] === option.value ? 'checked' : '';
        html += `
            <label class="flex items-center p-4 rounded-lg glassmorphism border border-emerald-400/30 hover:bg-white/10 transition-all duration-200 cursor-pointer">
                <input type="radio" 
                       name="question_${question.id}" 
                       value="${option.value}" 
                       ${isChecked}
                       onchange="selectAnswer(${question.id}, ${option.value})"
                       class="w-4 h-4 text-emerald-500 bg-emerald-600/20 border-emerald-400 focus:ring-emerald-500">
                <span class="ml-3 text-emerald-100">${option.text}</span>
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
