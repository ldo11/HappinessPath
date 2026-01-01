@extends('layouts.admin')

@section('title', 'Edit Daily Mission')
@section('page-title', 'Edit Daily Mission')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.daily-missions.update', $mission) }}">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Edit Daily Mission</h3>
            </div>

            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $mission->title) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $mission->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="points" class="block text-sm font-medium text-gray-700">Points</label>
                    <input type="number" name="points" id="points" required min="0" value="{{ old('points', $mission->points) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('points')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pillars</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <input type="hidden" name="is_body" value="0">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_body" value="1" class="rounded border-gray-300" @checked((bool) old('is_body', $mission->is_body))>
                            <span>Body</span>
                        </label>

                        <input type="hidden" name="is_mind" value="0">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_mind" value="1" class="rounded border-gray-300" @checked((bool) old('is_mind', $mission->is_mind))>
                            <span>Mind</span>
                        </label>

                        <input type="hidden" name="is_wisdom" value="0">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_wisdom" value="1" class="rounded border-gray-300" @checked((bool) old('is_wisdom', $mission->is_wisdom))>
                            <span>Wisdom</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.daily-missions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Back</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
