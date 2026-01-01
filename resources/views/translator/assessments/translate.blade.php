@extends('layouts.translator')

@section('title', 'Translate Assessment')
@section('page-title', 'Translate Assessment')

@section('content')
@php
    $detectPair = function (?string $en, ?string $vi): array {
        $en = trim((string) $en);
        $vi = trim((string) $vi);
        if ($en !== '' && $vi === '') {
            return ['en', 'vi', false];
        }
        if ($vi !== '' && $en === '') {
            return ['vi', 'en', false];
        }
        return ['en', 'vi', true];
    };

    [$assessmentSource, $assessmentTarget, $assessmentBoth] = $detectPair(
        $assessment->getTranslation('title', 'en'),
        $assessment->getTranslation('title', 'vi'),
    );

    $label = fn (string $locale) => strtoupper($locale) === 'VI' ? 'Vietnamese' : 'English';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Translate Assessment</h2>
    <p class="text-sm text-gray-600 mt-1">
        "{{ $assessment->getTranslation('title', 'en') ?: $assessment->getTranslation('title', 'vi') }}" - {{ $assessment->questions->count() }} questions
    </p>
</div>

<form method="POST" action="{{ route('translator.assessments.submit-translation', $assessment) }}" id="translationForm">
    @csrf

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Section 1: General Info</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">
                    {{ $assessmentBoth ? 'Original (EN)' : ('Original ('.$label($assessmentSource).')') }}
                </h4>

                <label class="block text-xs text-gray-600 mb-1">Title</label>
                <input type="text" value="{{ $assessment->getTranslation('title', $assessmentSource) }}" readonly
                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800" />

                <label class="block text-xs text-gray-600 mt-4 mb-1">Description</label>
                <textarea rows="4" readonly
                          class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800">{{ $assessment->getTranslation('description', $assessmentSource) }}</textarea>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">
                    {{ $assessmentBoth ? 'Translation (VI)' : ('Translation ('.$label($assessmentTarget).')') }}
                </h4>

                <label class="block text-xs text-gray-600 mb-1">Title</label>
                <input type="text" name="title[{{ $assessmentTarget }}]" value="{{ old('title.'.$assessmentTarget, $assessment->getTranslation('title', $assessmentTarget)) }}"
                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                <label class="block text-xs text-gray-600 mt-4 mb-1">Description</label>
                <textarea name="description[{{ $assessmentTarget }}]" rows="4"
                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description.'.$assessmentTarget, $assessment->getTranslation('description', $assessmentTarget)) }}</textarea>

                @if($assessmentBoth)
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 mb-2">Both EN and VI exist. You can edit either side.</div>
                        <label class="block text-xs text-gray-600 mb-1">English (Editable)</label>
                        <input type="text" name="title[en]" value="{{ old('title.en', $assessment->getTranslation('title', 'en')) }}"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <label class="block text-xs text-gray-600 mt-3 mb-1">Description (EN)</label>
                        <textarea name="description[en]" rows="3"
                                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description.en', $assessment->getTranslation('description', 'en')) }}</textarea>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Section 2: Questions Matrix</h3>

        @foreach($assessment->questions->sortBy('order') as $index => $question)
            @php
                [$qSource, $qTarget, $qBoth] = $detectPair(
                    $question->getTranslation('content', 'en'),
                    $question->getTranslation('content', 'vi')
                );
            @endphp

            <div class="mb-8 pb-8 border-b border-gray-200 last:border-b-0">
                <h4 class="text-md font-medium text-gray-900 mb-4">Question {{ $index + 1 }}</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Original ({{ $label($qSource) }})</label>
                        <textarea rows="2" readonly
                                  class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800">{{ $question->getTranslation('content', $qSource) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Translation ({{ $label($qTarget) }})</label>
                        <textarea name="questions[{{ $question->id }}][content][{{ $qTarget }}]" rows="2"
                                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old("questions.{$question->id}.content.{$qTarget}", $question->getTranslation('content', $qTarget)) }}</textarea>

                        @if($qBoth)
                            <div class="mt-3">
                                <label class="block text-xs text-gray-600 mb-1">English (Editable)</label>
                                <textarea name="questions[{{ $question->id }}][content][en]" rows="2"
                                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old("questions.{$question->id}.content.en", $question->getTranslation('content', 'en')) }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Nested Answers</label>

                    @foreach($question->answers as $answer)
                        @php
                            [$aSource, $aTarget, $aBoth] = $detectPair(
                                $answer->getTranslation('content', 'en'),
                                $answer->getTranslation('content', 'vi')
                            );
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Original ({{ $label($aSource) }})</label>
                                <input type="text" value="{{ $answer->getTranslation('content', $aSource) }}" readonly
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Translation ({{ $label($aTarget) }})</label>
                                <input type="text" name="answers[{{ $answer->id }}][content][{{ $aTarget }}]" value="{{ old("answers.{$answer->id}.content.{$aTarget}", $answer->getTranslation('content', $aTarget)) }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                                @if($aBoth)
                                    <div class="mt-2">
                                        <label class="block text-xs text-gray-600 mb-1">English (Editable)</label>
                                        <input type="text" name="answers[{{ $answer->id }}][content][en]" value="{{ old("answers.{$answer->id}.content.en", $answer->getTranslation('content', 'en')) }}"
                                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                The "Original" column is auto-detected per field (EN or VI). Fill the missing language on the right.
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('translator.assessments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Back
                </a>
                <button type="button" id="ajaxSaveBtn" class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-bolt mr-2"></i>Save (AJAX)
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Save Translation
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

const ajaxBtn = document.getElementById('ajaxSaveBtn');
if (ajaxBtn) {
    ajaxBtn.addEventListener('click', async () => {
        ajaxBtn.disabled = true;
        const originalText = ajaxBtn.textContent;
        ajaxBtn.textContent = 'Saving...';

        try {
            const formData = new FormData(form);
            const res = await fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            if (!res.ok) {
                throw new Error('Save failed');
            }

            ajaxBtn.textContent = 'Saved';
            setTimeout(() => {
                ajaxBtn.textContent = originalText;
            }, 900);
        } catch (e) {
            ajaxBtn.textContent = 'Error';
            setTimeout(() => {
                ajaxBtn.textContent = originalText;
            }, 1200);
        } finally {
            ajaxBtn.disabled = false;
        }
    });
}
</script>
@endpush
