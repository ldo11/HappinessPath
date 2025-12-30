@extends('layouts.translator')

@section('title', 'Translate Assessment')
@section('page-title', 'Translate Assessment')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Translate Assessment</h2>
    <p class="text-sm text-gray-600 mt-1">
        "{{ $assessment->title }}" - {{ $assessment->questions_count }} questions
    </p>
</div>

<form method="POST" action="{{ route('translator.assessments.submit-translation', $assessment) }}" id="translationForm">
    @csrf
    
    <!-- Assessment Basic Info Translation -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Assessment Information</h3>
        
        <!-- Title Translation -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Title</label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Vietnamese (Source)</label>
                    <input type="text" value="{{ $assessment->getRawOriginal('title')['vi'] }}" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">English *</label>
                    <input type="text" name="title[en]" required
                           value="{{ $assessment->getRawOriginal('title')['en'] ?? old('title.en') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="English translation">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Chinese</label>
                    <input type="text" name="title[zh]"
                           value="{{ $assessment->getRawOriginal('title')['zh'] ?? old('title.zh') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Chinese translation">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Korean</label>
                    <input type="text" name="title[ko]"
                           value="{{ $assessment->getRawOriginal('title')['ko'] ?? old('title.ko') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Korean translation">
                </div>
            </div>
        </div>

        <!-- Description Translation -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Description</label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Vietnamese (Source)</label>
                    <textarea readonly rows="3"
                              class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">{{ $assessment->getRawOriginal('description')['vi'] }}</textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">English *</label>
                    <textarea name="description[en]" required rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="English translation">{{ $assessment->getRawOriginal('description')['en'] ?? old('description.en') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Chinese</label>
                    <textarea name="description[zh]" rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Chinese translation">{{ $assessment->getRawOriginal('description')['zh'] ?? old('description.zh') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Korean</label>
                    <textarea name="description[ko]" rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Korean translation">{{ $assessment->getRawOriginal('description')['ko'] ?? old('description.ko') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Translation -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Questions & Options</h3>
        
        @foreach($assessment->questions->sortBy('order') as $index => $question)
        <div class="mb-8 pb-8 border-b border-gray-200 last:border-b-0">
            <h4 class="text-md font-medium text-gray-900 mb-4">Question {{ $index + 1 }}</h4>
            
            <!-- Question Content Translation -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Question Text</label>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Vietnamese (Source)</label>
                        <textarea readonly rows="2"
                                  class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">{{ $question->getRawOriginal('content')['vi'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">English *</label>
                        <textarea name="questions[{{ $index }}][content][en]" required rows="2"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="English translation">{{ $question->getRawOriginal('content')['en'] ?? old("questions.$index.content.en") }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Chinese</label>
                        <textarea name="questions[{{ $index }}][content][zh]" rows="2"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Chinese translation">{{ $question->getRawOriginal('content')['zh'] ?? old("questions.$index.content.zh") }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Korean</label>
                        <textarea name="questions[{{ $index }}][content][ko]" rows="2"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Korean translation">{{ $question->getRawOriginal('content')['ko'] ?? old("questions.$index.content.ko") }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Options Translation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Options</label>
                @foreach($question->options as $optionIndex => $option)
                <div class="mb-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Vietnamese (Source)</label>
                            <input type="text" value="{{ $option->getRawOriginal('content')['vi'] }}" readonly
                                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">English *</label>
                            <input type="text" name="questions[{{ $index }}][options][{{ $optionIndex }}][content][en]" required
                                   value="{{ $option->getRawOriginal('content')['en'] ?? old("questions.$index.options.$optionIndex.content.en") }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="English translation">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Chinese</label>
                            <input type="text" name="questions[{{ $index }}][options][{{ $optionIndex }}][content][zh]"
                                   value="{{ $option->getRawOriginal('content')['zh'] ?? old("questions.$index.options.$optionIndex.content.zh") }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Chinese translation">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Korean</label>
                            <input type="text" name="questions[{{ $index }}][options][{{ $optionIndex }}][content][ko]"
                                   value="{{ $option->getRawOriginal('content')['ko'] ?? old("questions.$index.options.$optionIndex.content.ko") }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Korean translation">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Form Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Vietnamese (Source) is read-only. English translation is required. Chinese and Korean are optional.
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('translator.assessments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Translation
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Auto-save functionality (optional enhancement)
let autoSaveTimer;
const form = document.getElementById('translationForm');

function startAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        // Save to localStorage for recovery
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem('assessmentTranslation_' + {{ $assessment->id }}, JSON.stringify(data));
        console.log('Auto-saved translation progress');
    }, 30000); // Auto-save after 30 seconds of inactivity
}

// Add event listeners for auto-save
form.addEventListener('input', startAutoSave);
form.addEventListener('change', startAutoSave);

// Load auto-saved data on page load
window.addEventListener('load', () => {
    const savedData = localStorage.getItem('assessmentTranslation_' + {{ $assessment->id }});
    if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input && !input.value) {
                input.value = data[key];
            }
        });
    }
});

// Clear auto-saved data on successful submission
form.addEventListener('submit', () => {
    localStorage.removeItem('assessmentTranslation_' + {{ $assessment->id }});
});
</script>
@endpush
