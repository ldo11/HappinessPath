@extends('layouts.translator')

@section('title', 'Translate Daily Mission')
@section('page-title', 'Translate Daily Mission')

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

    [$source, $target, $both] = $detectPair(
        $mission->getTranslation('title', 'en'),
        $mission->getTranslation('title', 'vi'),
    );

    $label = fn (string $locale) => strtoupper($locale) === 'VI' ? 'Vietnamese' : 'English';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Translate Daily Mission</h2>
    <p class="text-sm text-gray-600 mt-1">Side-by-side translation (auto-detect source language)</p>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('translator.daily-missions.update', $mission) }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Original ({{ $label($source) }})</h3>

                <label class="block text-xs text-gray-600 mb-1">Title</label>
                <input type="text" value="{{ $mission->getTranslation('title', $source) }}" readonly
                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800" />

                <label class="block text-xs text-gray-600 mt-4 mb-1">Description</label>
                <textarea rows="6" readonly
                          class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800">{{ $mission->getTranslation('description', $source) }}</textarea>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Translation ({{ $label($target) }})</h3>

                <label class="block text-xs text-gray-600 mb-1">Title</label>
                <input type="text" name="title[{{ $target }}]" value="{{ old('title.'.$target, $mission->getTranslation('title', $target)) }}"
                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                <label class="block text-xs text-gray-600 mt-4 mb-1">Description</label>
                <textarea name="description[{{ $target }}]" rows="6"
                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description.'.$target, $mission->getTranslation('description', $target)) }}</textarea>

                @if($both)
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 mb-2">Both EN and VI exist. You can edit either side.</div>
                        <label class="block text-xs text-gray-600 mb-1">English (Editable)</label>
                        <input type="text" name="title[en]" value="{{ old('title.en', $mission->getTranslation('title', 'en')) }}"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                        <label class="block text-xs text-gray-600 mt-3 mb-1">Description (EN)</label>
                        <textarea name="description[en]" rows="4"
                                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description.en', $mission->getTranslation('description', 'en')) }}</textarea>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('translator.daily-missions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                Back
            </a>
            <div class="flex gap-2">
                <button type="button" id="ajaxSaveBtn" class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-bolt mr-2"></i>Save (AJAX)
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Save Translation
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const form = document.querySelector('form');
const ajaxBtn = document.getElementById('ajaxSaveBtn');

if (form && ajaxBtn) {
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
