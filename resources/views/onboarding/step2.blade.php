@extends('layouts.app')

@section('title', 'Assessment - Your Happiness Path')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Progress Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Step 2 of 3</span>
                <span class="text-sm text-gray-500">Assessment Quiz</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-500 h-2 rounded-full transition-all duration-300" style="width: 66%"></div>
            </div>
        </div>

        <!-- Quiz Introduction -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-wisdom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-brain text-wisdom-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Understanding Your Path</h1>
            <p class="text-gray-600">Answer these 30 questions to help us personalize your journey</p>
            <p class="text-sm text-gray-500 mt-2">This will take about 10-15 minutes</p>
        </div>

        <!-- Quiz Form -->
        <form method="POST" action="{{ route('onboarding.step2.submit') }}" id="quizForm">
            @csrf
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <!-- Question Navigation -->
                <div class="mb-6 sticky top-20 z-30 bg-white pb-4 border-b">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                            Question <span id="currentQuestion">1</span> of {{ $allQuestions->count() }}
                        </span>
                        <div class="flex space-x-2">
                            <button type="button" onclick="previousQuestion()" 
                                    id="prevBtn"
                                    class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-chevron-left mr-1"></i>Previous
                            </button>
                            <button type="button" onclick="nextQuestion()" 
                                    id="nextBtn"
                                    class="px-3 py-1 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                Next<i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Progress Dots -->
                    <div class="flex justify-center mt-3 space-x-1" id="progressDots">
                        @foreach($allQuestions as $index => $question)
                            <div class="w-2 h-2 rounded-full transition-colors duration-200 
                                {{ $index === 0 ? 'bg-primary-500' : 'bg-gray-300' }}"
                                 data-question="{{ $index }}"></div>
                        @endforeach
                    </div>
                </div>

                <!-- Questions Container -->
                <div class="space-y-8" id="questionsContainer">
                    @foreach($allQuestions as $index => $question)
                        <div class="question-slide" data-question="{{ $index }}" 
                             {{ $index !== 0 ? 'style="display:none;"' : '' }}>
                            <!-- Question Header -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $question->pillar_group === 'heart' ? 'bg-heart-100 text-heart-800' :
                                           ($question->pillar_group === 'grit' ? 'bg-grit-100 text-grit-800' : 'bg-wisdom-100 text-wisdom-800') }}">
                                        <i class="fas fa-{{ $question->pillar_group === 'heart' ? 'heart' : ($question->pillar_group === 'grit' ? 'fire' : 'brain') }} mr-1"></i>
                                        {{ ucfirst($question->pillar_group) }}
                                    </span>
                                    <span class="text-sm text-gray-500">Question {{ $index + 1 }}</span>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 leading-relaxed">
                                    {{ $question->content }}
                                </h3>
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-3">
                                @foreach($question->answers->sortBy('score') as $answer)
                                    <label class="answer-option block p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                                        <div class="flex items-center">
                                            <input type="radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   value="{{ $answer->id }}"
                                                   class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                                   {{ $index === 0 && $loop->first ? 'required' : '' }}
                                                   onchange="selectAnswer({{ $question->id }}, {{ $answer->id }}, this)">
                                            <span class="ml-3 text-gray-700">{{ $answer->content }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button (only shown on last question) -->
                <div class="mt-8 pt-6 border-t" id="submitSection" style="display:none;">
                    <button type="submit" 
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center text-lg">
                        <i class="fas fa-chart-line mr-2"></i>
                        See My Results
                    </button>
                    <p class="text-center text-sm text-gray-500 mt-2">
                        Your personalized path awaits!
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
let currentQuestionIndex = 0;
const totalQuestions = {{ $allQuestions->count() }};
const selectedAnswers = {};

function showQuestion(index) {
    // Hide all questions
    document.querySelectorAll('.question-slide').forEach(slide => {
        slide.style.display = 'none';
    });
    
    // Show current question
    document.querySelector(`.question-slide[data-question="${index}"]`).style.display = 'block';
    
    // Update progress
    document.getElementById('currentQuestion').textContent = index + 1;
    
    // Update progress dots
    document.querySelectorAll('#progressDots > div').forEach((dot, i) => {
        if (i <= index) {
                            dot.classList.add('bg-primary-500');
                            dot.classList.remove('bg-gray-300');
                        } else {
                            dot.classList.remove('bg-primary-500');
                            dot.classList.add('bg-gray-300');
                        }
                    });
    
    // Update navigation buttons
    document.getElementById('prevBtn').disabled = index === 0;
    
    const nextBtn = document.getElementById('nextBtn');
    const submitSection = document.getElementById('submitSection');
    
    if (index === totalQuestions - 1) {
        nextBtn.style.display = 'none';
        submitSection.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitSection.style.display = 'none';
    }
    
    // Scroll to top of question
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextQuestion() {
    if (currentQuestionIndex < totalQuestions - 1) {
        currentQuestionIndex++;
        showQuestion(currentQuestionIndex);
    }
}

function previousQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showQuestion(currentQuestionIndex);
    }
}

function selectAnswer(questionId, answerId, element) {
    // Store selected answer
    selectedAnswers[questionId] = answerId;
    
    // Update visual state
    const questionContainer = element.closest('.question-slide');
    questionContainer.querySelectorAll('.answer-option').forEach(option => {
        option.classList.remove('border-primary-500', 'bg-primary-50');
        option.classList.add('border-gray-200');
    });
    
    element.closest('.answer-option').classList.remove('border-gray-200');
    element.closest('.answer-option').classList.add('border-primary-500', 'bg-primary-50');
    
    // Auto-advance to next question after a short delay
    setTimeout(() => {
        if (currentQuestionIndex < totalQuestions - 1) {
            nextQuestion();
        }
    }, 300);
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' && currentQuestionIndex < totalQuestions - 1) {
        nextQuestion();
    } else if (e.key === 'ArrowLeft' && currentQuestionIndex > 0) {
        previousQuestion();
    }
});

// Prevent form submission if not all questions are answered
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const answeredQuestions = Object.keys(selectedAnswers).length;
    if (answeredQuestions < totalQuestions) {
        e.preventDefault();
        alert(`Please answer all ${totalQuestions} questions before continuing.`);
        // Jump to first unanswered question
        for (let i = 0; i < totalQuestions; i++) {
            const questionSlide = document.querySelector(`.question-slide[data-question="${i}"]`);
            const questionId = questionSlide.querySelector('input[type="radio"]').name.match(/\d+/)[0];
            if (!selectedAnswers[questionId]) {
                currentQuestionIndex = i;
                showQuestion(currentQuestionIndex);
                break;
            }
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showQuestion(0);
});
</script>
@endsection
@endsection
