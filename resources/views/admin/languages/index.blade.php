@extends('layouts.admin')

@section('title', 'Manage Languages')
@section('page-title', 'Manage Languages')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Languages</h2>
    <a href="{{ route('admin.languages.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-2"></i>Add Language
    </a>
</div>

<!-- Languages Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Translations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($languages as $language)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-language text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $language->name }}</div>
                                @if($language->is_default)
                                    <span class="text-xs text-purple-600"><i class="fas fa-star"></i> Default</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $language->code }}</code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $language->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $language->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="space-y-1">
                            <div><span class="font-medium">{{ $language->solution_translations_count }}</span> solution translations</div>
                            <div><span class="font-medium">{{ $language->ui_translations_count }}</span> UI translations</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.languages.edit', $language) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('admin.languages.toggle', $language) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-{{ $language->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $language->is_active ? 'orange' : 'green' }}-900 mr-3">
                                <i class="fas fa-{{ $language->is_active ? 'pause' : 'play' }}"></i> {{ $language->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        @if($language->solution_translations_count == 0 && $language->ui_translations_count == 0)
                            <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this language?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($languages->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-language text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No languages found. Add your first language to get started.</p>
        <a href="{{ route('admin.languages.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Add Language
        </a>
    </div>
@endif
@endsection
