@extends('layouts.admin')

@section('title', 'Edit Question')
@section('page-title', 'Edit Question')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-800">Edit Question</h2>
    <a href="{{ route('admin.assessments.show', $assessment) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Back</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.assessments.questions.update', [$assessment, $question]) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Question (VI)</label>
                <input type="text" name="content[vi]" value="{{ old('content.vi', $question->getRawOriginal('content')['vi'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg" />
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Question (EN)</label>
                <input type="text" name="content[en]" value="{{ old('content.en', $question->getRawOriginal('content')['en'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 border rounded-lg">
                    <option value="single_choice" @selected(old('type', $question->type) === 'single_choice')>Single Choice</option>
                    <option value="multi_choice" @selected(old('type', $question->type) === 'multi_choice')>Multiple Choice</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Order</label>
                <input type="number" name="order" min="1" value="{{ old('order', $question->order) }}" class="w-full px-3 py-2 border rounded-lg" />
            </div>
        </div>

        <div>
            <div class="text-sm text-gray-700 mb-2">Answers (min 2)</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($question->options as $idx => $opt)
                    <div class="border rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-800 mb-2">Answer {{ $idx + 1 }}</div>
                        <input type="text" name="options[{{ $idx }}][content][vi]" placeholder="VI" value="{{ old('options.' . $idx . '.content.vi', $opt->getRawOriginal('content')['vi'] ?? '') }}" class="w-full px-3 py-2 border rounded-lg" />
                        <input type="text" name="options[{{ $idx }}][content][en]" placeholder="EN" value="{{ old('options.' . $idx . '.content.en', $opt->getRawOriginal('content')['en'] ?? '') }}" class="mt-2 w-full px-3 py-2 border rounded-lg" />
                        <input type="number" name="options[{{ $idx }}][score]" min="1" max="5" value="{{ old('options.' . $idx . '.score', $opt->score) }}" class="mt-2 w-24 px-3 py-2 border rounded-lg" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update Question</button>
        </div>
    </form>
</div>
@endsection
