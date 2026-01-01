@extends('layouts.app')

@section('title', 'Assessment Detail')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ $assessment->title }}</h2>
            <p class="text-white/60 mt-1">{{ $assessment->description }}</p>
            <div class="mt-3 text-sm text-white/70">Status: <span class="font-semibold">{{ $assessment->status }}</span></div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('consultant.assessments.edit', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Edit Assessment</a>
            <a href="{{ route('consultant.assessments.export-json', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="px-4 py-2 rounded-xl border border-white/15 text-white/80 hover:text-white hover:bg-white/5">Download JSON</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Questions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Question</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Answers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($assessment->questions as $q)
                        <tr>
                            <td class="px-6 py-4 text-white/80">{{ $q->order }}</td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $q->content }}</div>
                            </td>
                            <td class="px-6 py-4 text-white/80">{{ $q->type }}</td>
                            <td class="px-6 py-4 text-white/80">
                                {{ $q->options->count() }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a class="text-emerald-300 hover:text-emerald-200" href="{{ route('consultant.assessments.questions.edit', ['locale' => app()->getLocale(), 'assessment' => $assessment, 'question' => $q]) }}">Edit</a>
                                <form method="POST" action="{{ route('consultant.assessments.questions.destroy', ['locale' => app()->getLocale(), 'assessment' => $assessment, 'question' => $q]) }}" class="inline" onsubmit="return confirm('Delete this question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 text-red-300 hover:text-red-200">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-white/60">No questions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 border-t border-white/10 pt-6">
            <h4 class="text-white font-semibold mb-3">Add Question</h4>

            <form method="POST" action="{{ route('consultant.assessments.questions.store', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/80 mb-1">Question (VI)</label>
                        <input type="text" name="content[vi]" value="{{ old('content.vi') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm text-white/80 mb-1">Question (EN)</label>
                        <input type="text" name="content[en]" value="{{ old('content.en') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/80 mb-1">Type</label>
                        <select name="type" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                            <option class="text-gray-900" value="single_choice">Single Choice</option>
                            <option class="text-gray-900" value="multi_choice">Multiple Choice</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-white/80 mb-1">Order</label>
                        <input type="number" name="order" min="1" value="{{ old('order', ($assessment->questions->max('order') ?? 0) + 1) }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3">
                    </div>
                </div>

                <div>
                    <div class="text-sm text-white/80 mb-2">Answers (min 2)</div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                            <div class="text-white/80 text-sm font-medium mb-2">Answer 1</div>
                            <input type="text" name="options[0][content][vi]" placeholder="VI" value="{{ old('options.0.content.vi') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="text" name="options[0][content][en]" placeholder="EN" value="{{ old('options.0.content.en') }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="number" name="options[0][score]" min="1" max="5" value="{{ old('options.0.score', 1) }}" class="mt-2 w-24 rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                        </div>

                        <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                            <div class="text-white/80 text-sm font-medium mb-2">Answer 2</div>
                            <input type="text" name="options[1][content][vi]" placeholder="VI" value="{{ old('options.1.content.vi') }}" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="text" name="options[1][content][en]" placeholder="EN" value="{{ old('options.1.content.en') }}" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                            <input type="number" name="options[1][score]" min="1" max="5" value="{{ old('options.1.score', 2) }}" class="mt-2 w-24 rounded-xl bg-white/10 border border-white/15 text-white px-4 py-2">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
