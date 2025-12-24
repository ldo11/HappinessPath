@extends('layouts.translator')

@section('title', 'Translation Review')
@section('page-title', 'Translation Review')

@php
    $pendingCount = \App\Models\SolutionTranslation::where('is_auto_generated', true)
        ->whereNull('reviewed_at')
        ->count();
@endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Translation Review</h2>
    @if($pendingCount > 0)
        <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg">
            <i class="fas fa-clock mr-2"></i>{{ $pendingCount }} pending reviews
        </div>
    @endif
</div>

<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('translator.translations.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" placeholder="Search by title or content..."
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <select name="language" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Languages</option>
                <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>English</option>
                <option value="de" {{ request('language') == 'de' ? 'selected' : '' }}>German</option>
                <option value="fr" {{ request('language') == 'fr' ? 'selected' : '' }}>French</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <a href="{{ route('translator.translations.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
            Clear
        </a>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Translation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($translations as $translation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-language text-indigo-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($translation->title, 60) }}</div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-robot text-indigo-500"></i> Auto-generated
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs bg-gray-100 rounded-full">
                            {{ strtoupper($translation->locale) }}
                        </span>
                        <div class="text-xs text-gray-500">{{ $translation->language->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-{{ $translation->solution->type === 'video' ? 'video' : 'file-alt' }} text-gray-400 mr-2"></i>
                            {{ $translation->solution->type }}
                            <span class="ml-2 px-2 py-1 text-xs rounded-full
                                {{ $translation->solution->pillar_tag === 'heart' ? 'bg-red-100 text-red-800' :
                                   ($translation->solution->pillar_tag === 'grit' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($translation->solution->pillar_tag) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $translation->created_at->format('M j, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('translator.translations.review', $translation) }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-edit mr-1"></i> Review
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $translations->links() }}
    </div>
</div>

@if($translations->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
        <p class="text-gray-600">No translations to review!</p>
        <p class="text-sm text-gray-500">All auto-generated translations have been reviewed.</p>
    </div>
@endif
@endsection
