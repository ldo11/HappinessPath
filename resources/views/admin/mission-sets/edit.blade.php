@extends('layouts.admin')

@section('title', 'Edit Mission Set')
@section('page-title', 'Edit Mission Set')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.mission-sets.index') }}" class="text-gray-600 hover:text-gray-900 text-sm flex items-center gap-1 mb-2">
            <i class="fas fa-arrow-left"></i> Back to Mission Sets
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.mission-sets.update', $missionSet) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Set Name</label>
                    <input type="text" name="name" value="{{ old('name', $missionSet->getTranslation('name', 'en')) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $missionSet->getTranslation('description', 'en')) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="standard" {{ $missionSet->type === 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ $missionSet->type === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="special" {{ $missionSet->type === 'special' ? 'selected' : '' }}>Special Event</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $missionSet->is_default ? 'checked' : '' }}>
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                        Set as Default Program (Auto-assigned to new users)
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.mission-sets.index') }}" class="px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition">Cancel</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-md shadow-sm transition">Update Mission Set</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
