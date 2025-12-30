@extends('layouts.translator')

@section('title', 'Assessment Translations')
@section('page-title', 'Assessment Translations')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Assessment Translations</h2>
    <p class="text-sm text-gray-600 mt-1">Translate assessments for different languages</p>
</div>

<!-- Assessments Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($assessments as $assessment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $assessment->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($assessment->description, 80) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $assessment->creator->name }}</div>
                                <div class="text-sm text-gray-500">{{ $assessment->creator->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $assessment->questions_count }} questions</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($assessment->status)
                            @case('created')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-edit mr-1"></i>Pending Translation
                                </span>
                                @break
                            @case('translated')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-language mr-1"></i>Translated
                                </span>
                                @break
                            @default
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $assessment->created_at->format('M j, Y') }}
                        <div class="text-xs text-gray-400">{{ $assessment->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if(in_array($assessment->status, ['created', 'translated']))
                            <a href="{{ route('translator.assessments.translate', $assessment) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                                <i class="fas fa-language mr-1"></i>
                                {{ $assessment->status === 'created' ? 'Translate' : 'Edit Translation' }}
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">
                                <i class="fas fa-check-circle mr-1"></i>Completed
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($assessments->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-language text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No assessments need translation</h3>
        <p class="text-gray-600">All assessments are currently translated or being reviewed.</p>
    </div>
@endif

<!-- Translation Progress -->
<div class="mt-6 bg-white rounded-lg shadow p-4">
    <h4 class="text-sm font-medium text-gray-900 mb-3">Translation Status:</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div class="flex items-center">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">Created</span>
            <span class="text-gray-600">Ready for translation</span>
        </div>
        <div class="flex items-center">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">Translated</span>
            <span class="text-gray-600">Completed, waiting for review</span>
        </div>
        <div class="flex items-center">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2">Reviewed</span>
            <span class="text-gray-600">Approved by admin</span>
        </div>
    </div>
</div>
@endsection
