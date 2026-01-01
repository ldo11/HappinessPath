@extends('layouts.app')

@section('title', 'Take Assessment')
@section('page-title', 'Take Assessment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Assessment Header -->
    <div class="glass-card rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $assessment->title }}</h1>
            <button onclick="confirmExit()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-4">{{ $assessment->description }}</p>
        <div class="flex items-center space-x-6 text-sm text-gray-500">
            <div>
                <i class="fas fa-question-circle mr-1"></i>
                {{ $assessment->questions->count() }} Questions
            </div>
            <div>
                <i class="fas fa-clock mr-1"></i>
                ~{{ $assessment->questions->count() * 2 }} minutes
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress</span>
            <span class="text-sm text-gray-500">
                Question <span id="currentQuestion">1</span> of {{ $assessment->questions->count() }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progressBar" class="bg-emerald-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form id="assessmentForm" method="POST" action="{{ route('user.assessments.submit', $assessment) }}">
        @csrf
        
        <!-- Questions Container -->
        <div id="questionsContainer" class="space-y-8">
            @foreach($assessment->questions->sortBy('order') as $index => $question)
                <div class="question-panel glass-card rounded-xl shadow-lg p-6 {{ $index === 0 ? '' : 'hidden' }}" 
                     data-question-index="{{ $index }}">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            Question {{ $index + 1 }}
                        </h3>
                        <p class="text-gray-700">{{ $question->content }}</p>
                        
                        @if($question->type === 'multi_choice')
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                You can select multiple options
                            </p>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @foreach($question->options->sortByDesc('score') as $optionIndex => $option)
                            <label class="option-label block p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-emerald-300 transition-colors">
                                <div class="flex items-center">
                                    <input type="{{ $question->type === 'multi_choice' ? 'checkbox' : 'radio' }}" 
                                           name="answers[{{ $question->id }}]{{ $question->type === 'multi_choice' ? '[]' : '' }}" 
                                           value="{{ $option->id }}"
                                           class="mr-3 text-emerald-600 focus:ring-emerald-500"
                                           {{ $question->type === 'multi_choice' ? '' : 'required' }}>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">{{ $option->content }}</div>
                                        @if($option->score > 1)
                                            <div class="text-sm text-emerald-600 mt-1">
                                                <i class="fas fa-star mr-1"></i>
                                                {{ $option->score }} points
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-8">
                        @if($index > 0)
                            <button type="button" onclick="previousQuestion({{ $index - 1 }})" 
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Previous
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if($index < $assessment->questions->count() - 1)
                            <button type="button" onclick="nextQuestion({{ $index + 1 }})" 
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Next<i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        @else
                            <button type="button" onclick="showSubmissionOptions()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-check mr-2"></i>Complete Assessment
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submission Options Modal -->
        <div id="submissionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="glass-card rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Choose Submission Mode</h3>
                <p class="text-gray-600 mb-6">
                    How would you like to handle your assessment results?
                </p>

                <div class="space-y-4">
                    <label class="block">
                        <input type="radio" name="submission_mode" value="self_review" checked class="mr-3">
                        <span class="font-medium text-gray-800">Self Review</span>
                        <p class="text-sm text-gray-600 mt-1">Save results for personal reflection</p>
                    </label>

                    <label class="block">
                        <input type="radio" name="submission_mode" value="submitted_for_consultation" class="mr-3">
                        <span class="font-medium text-gray-800">Submit for Consultation</span>
                        <p class="text-sm text-gray-600 mt-1">Get personalized advice from our consultants</p>
                    </label>
                </div>

                <div class="flex space-x-3 mt-8">
                    <button type="button" onclick="hideSubmissionOptions()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        Back
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Submit
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let currentQuestionIndex = 0;
const totalQuestions = {{ $assessment->questions->count() }};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    setupOptionListeners();
});

function updateProgress() {
    const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
}

function showQuestion(index) {
    // Hide all questions
    document.querySelectorAll('.question-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    // Show current question
    document.querySelector(`[data-question-index="${index}"]`).classList.remove('hidden');
    
    currentQuestionIndex = index;
    updateProgress();
}

function nextQuestion(index) {
    if (validateCurrentQuestion()) {
        showQuestion(index);
    } else {
        alert('Please answer the current question before proceeding.');
    }
}

function previousQuestion(index) {
    showQuestion(index);
}

function validateCurrentQuestion() {
    const currentPanel = document.querySelector(`[data-question-index="${currentQuestionIndex}"]`);
    const requiredInputs = currentPanel.querySelectorAll('input[required]');
    const checkedInputs = currentPanel.querySelectorAll('input:checked');
    
    return requiredInputs.length === 0 || checkedInputs.length > 0;
}

function showSubmissionOptions() {
    if (validateAllQuestions()) {
        document.getElementById('submissionModal').classList.remove('hidden');
    } else {
        alert('Please answer all questions before submitting.');
    }
}

function hideSubmissionOptions() {
    document.getElementById('submissionModal').classList.add('hidden');
}

function validateAllQuestions() {
    const allQuestions = document.querySelectorAll('.question-panel');
    
    for (let panel of allQuestions) {
        const requiredInputs = panel.querySelectorAll('input[required]');
        const checkedInputs = panel.querySelectorAll('input:checked');
        
        if (requiredInputs.length > 0 && checkedInputs.length === 0) {
            return false;
        }
    }
    
    return true;
}

function setupOptionListeners() {
    // Add visual feedback for option selection
    document.querySelectorAll('.option-label input').forEach(input => {
        input.addEventListener('change', function() {
            const label = this.closest('.option-label');
            const questionPanel = this.closest('.question-panel');
            
            if (this.type === 'radio') {
                // Uncheck all other options in this question
                questionPanel.querySelectorAll('.option-label').forEach(otherLabel => {
                    otherLabel.classList.remove('border-emerald-500', 'bg-emerald-50');
                });
            }
            
            if (this.checked) {
                label.classList.add('border-emerald-500', 'bg-emerald-50');
            } else {
                label.classList.remove('border-emerald-500', 'bg-emerald-50');
            }
        });
    });
}

function confirmExit() {
    if (confirm('Are you sure you want to exit? Your progress will be lost.')) {
        window.location.href = '{{ route("user.assessments.index") }}';
    }
}

// Prevent form submission on Enter key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.type !== 'submit') {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush
