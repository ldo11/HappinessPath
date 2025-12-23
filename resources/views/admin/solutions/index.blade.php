@extends('layouts.admin')

@section('title', 'Manage Solutions')
@section('page-title', 'Manage Solutions')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Solutions</h2>
    <a href="{{ route('admin.solutions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-2"></i>Add Solution
    </a>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.solutions.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" placeholder="Search by title, content, author..." 
                   value="{{ request('search') }}" 
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="type" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                <option value="article" {{ request('type') == 'article' ? 'selected' : '' }}>Article</option>
            </select>
        </div>
        <div>
            <select name="pillar_tag" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Pillars</option>
                <option value="heart" {{ request('pillar_tag') == 'heart' ? 'selected' : '' }}>Heart</option>
                <option value="grit" {{ request('pillar_tag') == 'grit' ? 'selected' : '' }}>Grit</option>
                <option value="wisdom" {{ request('pillar_tag') == 'wisdom' ? 'selected' : '' }}>Wisdom</option>
            </select>
        </div>
        <div>
            <select name="locale" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Languages</option>
                @foreach($languages as $language)
                    <option value="{{ $language->code }}" {{ request('locale') == $language->code ? 'selected' : '' }}>
                        {{ $language->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <a href="{{ route('admin.solutions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
            Clear
        </a>
    </form>
</div>

<!-- Solutions Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pillar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Languages</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($solutions as $solution)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $solution->type === 'video' ? 'video' : 'file-alt' }} text-purple-600"></i>
                            </div>
                            <div class="ml-4">
                                @foreach($solution->translations as $translation)
                                    @if($translation->locale === $solution->locale)
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($translation->title, 50) }}</div>
                                        <div class="text-sm text-gray-500">{{ $translation->locale }}</div>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst($solution->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $solution->pillar_tag === 'heart' ? 'bg-red-100 text-red-800' : 
                               ($solution->pillar_tag === 'grit' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($solution->pillar_tag) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            @foreach($solution->translations as $translation)
                                <span class="px-2 py-1 text-xs bg-gray-100 rounded">
                                    {{ strtoupper($translation->locale) }}
                                    @if($translation->is_auto_generated)
                                        <i class="fas fa-robot text-blue-500 text-xs"></i>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $solution->author_name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.solutions.show', $solution) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.solutions.edit', $solution) }}" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('admin.solutions.destroy', $solution) }}" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this solution?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $solutions->links() }}
    </div>
</div>

@if($solutions->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-video text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No solutions found. Add your first solution to get started.</p>
        <a href="{{ route('admin.solutions.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Solution
        </a>
    </div>
@endif
@endsection
