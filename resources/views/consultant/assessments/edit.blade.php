@extends('layouts.app')

@section('title', 'Edit Assessment')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Edit Assessment</h2>
            <p class="text-sm text-white/60 mt-1">Update assessment details and questions.</p>
        </div>
        <a href="{{ route('consultant.assessments.index', ['locale' => app()->getLocale()]) }}" class="text-white/80 hover:text-white">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultant.assessments.update', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" id="assessmentForm" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Title *</label>
                    <div class="grid grid-cols-1 gap-3">
                        <input type="text" name="title[vi]" value="{{ old('title.vi', $assessment->getRawOriginal('title')['vi'] ?? '') }}" placeholder="Vietnamese title"
                               class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                        <input type="text" name="title[en]" value="{{ old('title.en', $assessment->getRawOriginal('title')['en'] ?? '') }}" placeholder="English title"
                               class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Description *</label>
                    <div class="grid grid-cols-1 gap-3">
                        <textarea name="description[vi]" rows="3" placeholder="Vietnamese description"
                                  class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('description.vi', $assessment->getRawOriginal('description')['vi'] ?? '') }}</textarea>
                        <textarea name="description[en]" rows="3" placeholder="English description"
                                  class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('description.en', $assessment->getRawOriginal('description')['en'] ?? '') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                        <option class="text-gray-900" value="created" @selected(old('status', $assessment->status) === 'created')>Draft</option>
                        <option class="text-gray-900" value="active" @selected(old('status', $assessment->status) === 'active')>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-white">Questions</h3>
                    <button type="button" id="addQuestion" class="px-3 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-sm">Add Question</button>
                </div>

                <div id="questionsContainer" class="space-y-4"></div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let questionIndex = 0;
const questionsContainer = document.getElementById('questionsContainer');
const existingQuestions = @json($assessment->questions->load('options')->toArray());

function addQuestionBlock(questionData = null, qIndex = null) {
    const index = qIndex !== null ? qIndex : questionIndex++;
    const html = `
        <div class="rounded-2xl bg-white/5 border border-white/10 p-4" data-question-index="${index}">
            <div class="flex items-center justify-between mb-3">
                <div class="text-white font-semibold">Question ${index + 1}</div>
                <button type="button" class="text-red-300 hover:text-red-200" onclick="removeQuestion(${index})">Remove</button>
            </div>

            <input type="hidden" name="questions[${index}][order]" value="${questionData ? questionData.order : (index + 1)}">

            <div class="grid grid-cols-1 gap-3">
                <input type="text" name="questions[${index}][content][vi]" placeholder="Vietnamese question" value="${questionData && questionData.content ? (questionData.content.vi || '') : ''}"
                       class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                <input type="text" name="questions[${index}][content][en]" placeholder="English question" value="${questionData && questionData.content ? (questionData.content.en || '') : ''}"
                       class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-white/80 mb-2">Type</label>
                <select name="questions[${index}][type]" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <option value="single_choice" ${questionData && questionData.type === 'single_choice' ? 'selected' : ''}>Single Choice</option>
                    <option value="multi_choice" ${questionData && questionData.type === 'multi_choice' ? 'selected' : ''}>Multiple Choice</option>
                </select>
            </div>

            <div class="mt-4" data-options-for="${index}">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-white/80 text-sm font-medium">Options</div>
                    <button type="button" class="px-3 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100 text-xs" onclick="addOption(${index})">Add Option</button>
                </div>
                <div class="space-y-3 options-list"></div>
            </div>
        </div>
    `;

    questionsContainer.insertAdjacentHTML('beforeend', html);

    const options = questionData && questionData.options && questionData.options.length ? questionData.options : [{}, {}];
    options.forEach((opt, optIdx) => addOption(index, opt, optIdx));
}

function removeQuestion(index) {
    const el = document.querySelector(`[data-question-index="${index}"]`);
    if (el) el.remove();
}

function addOption(questionIdx, optionData = null, optIndex = null) {
    const wrapper = document.querySelector(`[data-options-for="${questionIdx}"] .options-list`);
    const optionIdx = optIndex !== null ? optIndex : wrapper.children.length;

    const vi = optionData && optionData.content ? (optionData.content.vi || '') : '';
    const en = optionData && optionData.content ? (optionData.content.en || '') : '';
    const score = optionData && optionData.score ? optionData.score : 1;

    const html = `
        <div class="rounded-xl bg-white/5 border border-white/10 p-3" data-option-index="${optionIdx}">
            <div class="grid grid-cols-1 gap-2">
                <input type="text" name="questions[${questionIdx}][options][${optionIdx}][content][vi]" placeholder="Vietnamese option" value="${vi}"
                       class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">
                <input type="text" name="questions[${questionIdx}][options][${optionIdx}][content][en]" placeholder="English option" value="${en}"
                       class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">
                <div class="flex items-center gap-3">
                    <label class="text-white/70 text-sm">Score</label>
                    <input type="number" name="questions[${questionIdx}][options][${optionIdx}][score]" value="${score}" min="1" max="5" required
                           class="w-24 rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <button type="button" class="ml-auto text-red-300 hover:text-red-200 text-sm" onclick="removeOption(${questionIdx}, ${optionIdx})">Remove</button>
                </div>
            </div>
        </div>
    `;

    wrapper.insertAdjacentHTML('beforeend', html);
}

function removeOption(questionIdx, optionIdx) {
    const wrapper = document.querySelector(`[data-options-for="${questionIdx}"] .options-list`);
    const el = wrapper?.querySelector(`[data-option-index="${optionIdx}"]`);
    if (el) el.remove();
}

document.getElementById('addQuestion').addEventListener('click', () => addQuestionBlock());

if (existingQuestions && existingQuestions.length) {
    questionIndex = existingQuestions.length;
    existingQuestions.forEach((q, idx) => addQuestionBlock(q, idx));
} else {
    addQuestionBlock();
}
</script>
@endpush
