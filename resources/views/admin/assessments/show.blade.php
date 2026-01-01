@extends('layouts.admin')

@section('title', 'Assessment Detail')
@section('page-title', 'Assessment Detail')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $assessment->title }}</h2>
        <p class="text-sm text-gray-600 mt-1">{{ $assessment->description }}</p>
        <div class="mt-2 text-sm text-gray-700">Status: <span class="font-semibold">{{ $assessment->status }}</span></div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('admin.assessments.edit', $assessment) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit mr-2"></i>Edit Assessment
        </a>
        <a href="{{ route('admin.assessments.export-json', $assessment) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
            <i class="fas fa-download mr-2"></i>Download JSON
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Questions</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Answers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assessment->questions as $q)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $q->order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $q->content }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $q->type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $q->options->count() }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.assessments.questions.edit', [$assessment, $q]) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form method="POST" action="{{ route('admin.assessments.questions.destroy', [$assessment, $q]) }}" class="inline" onsubmit="return confirm('Delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-sm text-gray-500">No questions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 border-t pt-6">
        <h4 class="text-gray-900 font-semibold mb-3">Add Question</h4>

        <form method="POST" action="{{ route('admin.assessments.questions.store', $assessment) }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Question (VI)</label>
                    <input type="text" name="content[vi]" value="{{ old('content.vi') }}" class="w-full px-3 py-2 border rounded-lg" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Question (EN)</label>
                    <input type="text" name="content[en]" value="{{ old('content.en') }}" class="w-full px-3 py-2 border rounded-lg" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 border rounded-lg">
                        <option value="single_choice">Single Choice</option>
                        <option value="multi_choice">Multiple Choice</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" min="1" value="{{ old('order', ($assessment->questions->max('order') ?? 0) + 1) }}" class="w-full px-3 py-2 border rounded-lg" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-800 mb-2">Answer 1</div>
                    <input type="text" name="options[0][content][vi]" placeholder="VI" value="{{ old('options.0.content.vi') }}" class="w-full px-3 py-2 border rounded-lg" />
                    <input type="text" name="options[0][content][en]" placeholder="EN" value="{{ old('options.0.content.en') }}" class="mt-2 w-full px-3 py-2 border rounded-lg" />
                    <input type="number" name="options[0][score]" min="1" max="5" value="{{ old('options.0.score', 1) }}" class="mt-2 w-24 px-3 py-2 border rounded-lg" />
                </div>

                <div class="border rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-800 mb-2">Answer 2</div>
                    <input type="text" name="options[1][content][vi]" placeholder="VI" value="{{ old('options.1.content.vi') }}" class="w-full px-3 py-2 border rounded-lg" />
                    <input type="text" name="options[1][content][en]" placeholder="EN" value="{{ old('options.1.content.en') }}" class="mt-2 w-full px-3 py-2 border rounded-lg" />
                    <input type="number" name="options[1][score]" min="1" max="5" value="{{ old('options.1.score', 2) }}" class="mt-2 w-24 px-3 py-2 border rounded-lg" />
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Add Question</button>
            </div>
        </form>
    </div>
</div>
@endsection
