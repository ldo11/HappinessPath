@extends('layouts.admin')

@section('title', 'Assessment Questions')
@section('page-title', 'Assessment Questions')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-gray-800">Assessment Questions</h2>
    <a href="{{ route('admin.assessment-questions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Create</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pillar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VI</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($questions as $q)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $q->id }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $q->pillar_group }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $q->order }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $q->getLocalizedContent('vi') }}</td>
                        <td class="px-6 py-3 text-sm">
                            <a class="text-blue-600" href="{{ route('admin.assessment-questions.edit', $q) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.assessment-questions.destroy', $q) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $questions->links() }}
    </div>
</div>
@endsection
