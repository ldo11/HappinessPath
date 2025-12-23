@extends('layouts.admin')

@section('title', 'Edit Language')
@section('page-title', 'Edit Language')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.languages.update', $language) }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Edit Language: {{ $language->name }}</h3>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Language Code</label>
                    <input type="text" name="code" id="code" required maxlength="10"
                           value="{{ old('code', $language->code) }}"
                           placeholder="e.g., en, vi, de"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">ISO 639-1 language code (2-10 characters)</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Language Name</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $language->name) }}"
                           placeholder="e.g., English, Vietnamese, German"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $language->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active (available for translations and UI)
                    </label>
                </div>

                <!-- Default Language -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           {{ old('is_default', $language->is_default) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                        Set as default language
                    </label>
                </div>
                <p class="text-sm text-gray-500">Only one language can be set as default. This will be used as the fallback language.</p>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.languages.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700">
                    Update Language
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
