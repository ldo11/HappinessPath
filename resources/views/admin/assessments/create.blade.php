@extends('layouts.admin')

@section('title', isset($assessment) ? 'Edit Assessment' : 'Create Assessment')
@section('page-title', isset($assessment) ? 'Edit Assessment' : 'Create Assessment')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        {{ isset($assessment) ? 'Edit Assessment' : 'Create Assessment' }}
    </h2>
    <p class="text-sm text-gray-600 mt-1">
        {{ isset($assessment) ? 'Update assessment details and questions' : 'Create a new assessment with questions and options' }}
    </p>
</div>

<form method="POST" action="{{ isset($assessment) ? route('admin.assessments.update', $assessment) : route('admin.assessments.store') }}" 
      id="assessmentForm" class="space-y-6">
    @csrf
    @if(isset($assessment))
        @method('PUT')
    @endif

    <!-- Basic Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="w-16 text-sm text-gray-600">Vietnamese:</span>
                        <input type="text" name="title[vi]" required
                               value="{{ isset($assessment) ? $assessment->getRawOriginal('title')['vi'] ?? '' : old('title.vi') }}"
                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Assessment title in Vietnamese">
                    </div>
                    <div class="flex items-center">
                        <span class="w-16 text-sm text-gray-600">English:</span>
                        <input type="text" name="title[en]"
                               value="{{ isset($assessment) ? $assessment->getRawOriginal('title')['en'] ?? '' : old('title.en') }}"
                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Assessment title in English">
                    </div>
                    <div class="flex items-center">
                        <span class="w-16 text-sm text-gray-600">Chinese:</span>
                        <input type="text" name="title[zh]"
                               value="{{ isset($assessment) ? $assessment->getRawOriginal('title')['zh'] ?? '' : old('title.zh') }}"
                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Assessment title in Chinese">
                    </div>
                    <div class="flex items-center">
                        <span class="w-16 text-sm text-gray-600">Korean:</span>
                        <input type="text" name="title[ko]"
                               value="{{ isset($assessment) ? $assessment->getRawOriginal('title')['ko'] ?? '' : old('title.ko') }}"
                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Assessment title in Korean">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <div class="space-y-2">
                    <div class="flex items-start">
                        <span class="w-16 text-sm text-gray-600 pt-2">Vietnamese:</span>
                        <textarea name="description[vi]" required rows="3"
                                  class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Assessment description in Vietnamese">{{ isset($assessment) ? $assessment->getRawOriginal('description')['vi'] ?? '' : old('description.vi') }}</textarea>
                    </div>
                    <div class="flex items-start">
                        <span class="w-16 text-sm text-gray-600 pt-2">English:</span>
                        <textarea name="description[en]" rows="3"
                                  class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Assessment description in English">{{ isset($assessment) ? $assessment->getRawOriginal('description')['en'] ?? '' : old('description.en') }}</textarea>
                    </div>
                    <div class="flex items-start">
                        <span class="w-16 text-sm text-gray-600 pt-2">Chinese:</span>
                        <textarea name="description[zh]" rows="3"
                                  class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Assessment description in Chinese">{{ isset($assessment) ? $assessment->getRawOriginal('description')['zh'] ?? '' : old('description.zh') }}</textarea>
                    </div>
                    <div class="flex items-start">
                        <span class="w-16 text-sm text-gray-600 pt-2">Korean:</span>
                        <textarea name="description[ko]" rows="3"
                                  class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Assessment description in Korean">{{ isset($assessment) ? $assessment->getRawOriginal('description')['ko'] ?? '' : old('description.ko') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Questions</h3>
            <button type="button" id="addQuestion" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                <i class="fas fa-plus mr-1"></i>Add Question
            </button>
        </div>

        <div id="questionsContainer" class="space-y-4">
            <!-- Questions will be dynamically added here -->
        </div>
    </div>

    <!-- Form Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.assessments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i>
                {{ isset($assessment) ? 'Update Assessment' : 'Create Assessment' }}
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let questionIndex = 0;
const questionsContainer = document.getElementById('questionsContainer');

// Load existing questions if editing
@if(isset($assessment))
    const existingQuestions = @json($assessment->questions->load('options')->toArray());
    questionIndex = existingQuestions.length;
    
    existingQuestions.forEach(function(question, qIndex) {
        addQuestionBlock(question, qIndex);
    });
@else
    // Add one empty question by default for new assessments
    addQuestionBlock();
@endif

function addQuestionBlock(questionData = null, qIndex = null) {
    const index = qIndex !== null ? qIndex : questionIndex++;
    const questionHtml = `
        <div class="question-block border border-gray-200 rounded-lg p-4" data-question-index="${index}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-medium text-gray-900">Question ${index + 1}</h4>
                <button type="button" onclick="removeQuestion(${index})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <input type="hidden" name="questions[${index}][order]" value="${index + 1}">

            <!-- Question Content -->
            <div class="space-y-2 mb-3">
                <div class="flex items-center">
                    <span class="w-20 text-sm text-gray-600">Vietnamese:</span>
                    <input type="text" name="questions[${index}][content][vi]" required
                           value="${questionData ? questionData.content.vi || '' : ''}"
                           class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Question in Vietnamese">
                </div>
                <div class="flex items-center">
                    <span class="w-20 text-sm text-gray-600">English:</span>
                    <input type="text" name="questions[${index}][content][en]"
                           value="${questionData ? questionData.content.en || '' : ''}"
                           class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Question in English">
                </div>
                <div class="flex items-center">
                    <span class="w-20 text-sm text-gray-600">Chinese:</span>
                    <input type="text" name="questions[${index}][content][zh]"
                           value="${questionData ? questionData.content.zh || '' : ''}"
                           class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Question in Chinese">
                </div>
                <div class="flex items-center">
                    <span class="w-20 text-sm text-gray-600">Korean:</span>
                    <input type="text" name="questions[${index}][content][ko]"
                           value="${questionData ? questionData.content.ko || '' : ''}"
                           class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Question in Korean">
                </div>
            </div>

            <!-- Question Type -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                <select name="questions[${index}][type]" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="single_choice" ${questionData && questionData.type === 'single_choice' ? 'selected' : ''}>Single Choice</option>
                    <option value="multi_choice" ${questionData && questionData.type === 'multi_choice' ? 'selected' : ''}>Multiple Choice</option>
                </select>
            </div>

            <!-- Options -->
            <div class="options-container" data-question-index="${index}">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Options</label>
                    <button type="button" onclick="addOption(${index})" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                        <i class="fas fa-plus mr-1"></i>Add Option
                    </button>
                </div>
                <div class="options-list space-y-2">
                    ${generateOptionsHtml(index, questionData ? questionData.options : null)}
                </div>
            </div>
        </div>
    `;
    
    questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
}

function generateOptionsHtml(questionIndex, optionsData = null) {
    let optionsHtml = '';
    const options = optionsData || [{}]; // Default one empty option
    
    options.forEach(function(option, optIndex) {
        optionsHtml += `
            <div class="option-block border border-gray-100 rounded p-2" data-option-index="${optIndex}">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Option ${optIndex + 1}</span>
                    ${options.length > 2 ? `<button type="button" onclick="removeOption(${questionIndex}, ${optIndex})" class="text-red-600 hover:text-red-800 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>` : ''}
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    <div>
                        <input type="text" name="questions[${questionIndex}][options][${optIndex}][content][vi]" required
                               value="${option && option.content ? option.content.vi || '' : ''}"
                               class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                               placeholder="Vietnamese">
                    </div>
                    <div>
                        <input type="text" name="questions[${questionIndex}][options][${optIndex}][content][en]"
                               value="${option && option.content ? option.content.en || '' : ''}"
                               class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                               placeholder="English">
                    </div>
                    <div>
                        <input type="text" name="questions[${questionIndex}][options][${optIndex}][content][zh]"
                               value="${option && option.content ? option.content.zh || '' : ''}"
                               class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                               placeholder="Chinese">
                    </div>
                    <div>
                        <input type="text" name="questions[${questionIndex}][options][${optIndex}][content][ko]"
                               value="${option && option.content ? option.content.ko || '' : ''}"
                               class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                               placeholder="Korean">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <label class="text-sm text-gray-600 mr-2">Score:</label>
                    <input type="number" name="questions[${questionIndex}][options][${optIndex}][score]" 
                           value="${option ? option.score || 1 : 1}"
                           min="1" max="5" required
                           class="w-16 px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                </div>
            </div>
        `;
    });
    
    return optionsHtml;
}

function addOption(questionIndex) {
    const optionsList = document.querySelector(`.question-block[data-question-index="${questionIndex}"] .options-list`);
    const optionCount = optionsList.children.length;
    const optionHtml = `
        <div class="option-block border border-gray-100 rounded p-2" data-option-index="${optionCount}">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Option ${optionCount + 1}</span>
                <button type="button" onclick="removeOption(${questionIndex}, ${optionCount})" class="text-red-600 hover:text-red-800 text-xs">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                <div>
                    <input type="text" name="questions[${questionIndex}][options][${optionCount}][content][vi]" required
                           class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                           placeholder="Vietnamese">
                </div>
                <div>
                    <input type="text" name="questions[${questionIndex}][options][${optionCount}][content][en]"
                           class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                           placeholder="English">
                </div>
                <div>
                    <input type="text" name="questions[${questionIndex}][options][${optionCount}][content][zh]"
                           class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                           placeholder="Chinese">
                </div>
                <div>
                    <input type="text" name="questions[${questionIndex}][options][${optionCount}][content][ko]"
                           class="w-full px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                           placeholder="Korean">
                </div>
            </div>
            
            <div class="flex items-center">
                <label class="text-sm text-gray-600 mr-2">Score:</label>
                <input type="number" name="questions[${questionIndex}][options][${optionCount}][score]" 
                       value="1" min="1" max="5" required
                       class="w-16 px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
            </div>
        </div>
    `;
    
    optionsList.insertAdjacentHTML('beforeend', optionHtml);
}

function removeOption(questionIndex, optionIndex) {
    const optionBlock = document.querySelector(`.question-block[data-question-index="${questionIndex}"] .option-block[data-option-index="${optionIndex}"]`);
    const optionsList = optionBlock.parentElement;
    
    // Remove the option
    optionBlock.remove();
    
    // Re-index remaining options
    Array.from(optionsList.children).forEach((option, newIndex) => {
        option.setAttribute('data-option-index', newIndex);
        option.querySelector('span').textContent = `Option ${newIndex + 1}`;
        
        // Update input names
        const inputs = option.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/options\]\[\d+\]/, `options][${newIndex}]`);
            input.setAttribute('name', newName);
        });
        
        // Update remove button onclick
        const removeBtn = option.querySelector('button[onclick*="removeOption"]');
        if (removeBtn) {
            removeBtn.setAttribute('onclick', `removeOption(${questionIndex}, ${newIndex})`);
        }
    });
}

function removeQuestion(index) {
    const questionBlock = document.querySelector(`.question-block[data-question-index="${index}"]`);
    questionBlock.remove();
    
    // Re-index remaining questions
    const remainingQuestions = document.querySelectorAll('.question-block');
    remainingQuestions.forEach((question, newIndex) => {
        question.setAttribute('data-question-index', newIndex);
        question.querySelector('h4').textContent = `Question ${newIndex + 1}`;
        
        // Update all input names in this question
        const inputs = question.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/questions\]\[\d+\]/, `questions][${newIndex}]`);
            input.setAttribute('name', newName);
        });
        
        // Update remove question button
        const removeBtn = question.querySelector('button[onclick*="removeQuestion"]');
        if (removeBtn) {
            removeBtn.setAttribute('onclick', `removeQuestion(${newIndex})`);
        }
        
        // Update add option button
        const addOptionBtn = question.querySelector('button[onclick*="addOption"]');
        if (addOptionBtn) {
            addOptionBtn.setAttribute('onclick', `addOption(${newIndex})`);
        }
        
        // Update options container
        const optionsContainer = question.querySelector('.options-container');
        if (optionsContainer) {
            optionsContainer.setAttribute('data-question-index', newIndex);
        }
        
        // Update option remove buttons
        const optionRemoveBtns = question.querySelectorAll('button[onclick*="removeOption"]');
        optionRemoveBtns.forEach(btn => {
            const onclick = btn.getAttribute('onclick');
            const newOnclick = onclick.replace(/removeOption\(\d+,/, `removeOption(${newIndex},`);
            btn.setAttribute('onclick', newOnclick);
        });
    });
}

// Add question button event listener
document.getElementById('addQuestion').addEventListener('click', function() {
    addQuestionBlock();
});
</script>
@endpush
