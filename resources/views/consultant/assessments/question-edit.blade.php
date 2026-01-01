@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Edit Question</h2>
        <a href="{{ route('consultant.assessments.show', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="text-white/80 hover:text-white">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultant.assessments.questions.update', ['locale' => app()->getLocale(), 'assessment' => $assessment, 'question' => $question]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/80 mb-1">Question (VI)</label>
                    <input type="text" name="content[vi]" value="{{ old('content.vi', $question->getRawOriginal('content')['vi'] ?? '') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm text-white/80 mb-1">Question (EN)</label>
                    <input type="text" name="content[en]" value="{{ old('content.en', $question->getRawOriginal('content')['en'] ?? '') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/80 mb-1">Type</label>
                    <select name="type" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                        <option class="text-gray-900" value="single_choice" @selected(old('type', $question->type) === 'single_choice')>Single Choice</option>
                        <option class="text-gray-900" value="multi_choice" @selected(old('type', $question->type) === 'multi_choice')>Multiple Choice</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-white/80 mb-1">Order</label>
                    <input type="number" name="order" min="1" value="{{ old('order', $question->order) }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                </div>
            </div>

            <div>
                <div class="text-sm text-white/80 mb-2">Answers (min 2)</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($question->options as $idx => $opt)
                        <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                            <div class="text-white/80 text-sm font-medium mb-2">Answer {{ $idx + 1 }}</div>
                            <input type="text" name="options[{{ $idx }}][content][vi]" placeholder="VI" value="{{ old('options.' . $idx . '.content.vi', $opt->getRawOriginal('content')['vi'] ?? '') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="text" name="options[{{ $idx }}][content][en]" placeholder="EN" value="{{ old('options.' . $idx . '.content.en', $opt->getRawOriginal('content')['en'] ?? '') }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="number" name="options[{{ $idx }}][score]" min="1" max="5" value="{{ old('options.' . $idx . '.score', $opt->score) }}" class="mt-2 w-24 rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Update Question</button>
            </div>
        </form>
    </div>
</div>
@endsection
