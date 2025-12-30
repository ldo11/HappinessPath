@extends('layouts.admin')

@section('title', 'Edit Video')
@section('page-title', 'Edit Video')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.videos.update', $video) }}">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Edit Video: {{ $video->title }}</h3>
            </div>

            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $video->title) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL (YouTube / Embed)</label>
                    <input type="text" name="url" id="url" required value="{{ old('url', $video->url) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                    <select name="language" id="language" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="vi" @selected(old('language', $video->language ?? 'vi') === 'vi')>Vietnamese (vi)</option>
                        <option value="en" @selected(old('language', $video->language) === 'en')>English (en)</option>
                        <option value="de" @selected(old('language', $video->language) === 'de')>German (de)</option>
                        <option value="kr" @selected(old('language', $video->language) === 'kr')>Korean (kr)</option>
                    </select>
                    @error('language')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    @php
                        $pillarTags = (array) old('pillar_tags', $video->pillar_tags ?? []);
                        $sourceTags = (array) old('source_tags', $video->source_tags ?? []);
                    @endphp

                    <label class="block text-sm font-medium text-gray-700">Pillars</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="pillar_tags[]" value="body" class="rounded border-gray-300" @checked(in_array('body', $pillarTags, true))>
                            <span>Body (Thân)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="pillar_tags[]" value="mind" class="rounded border-gray-300" @checked(in_array('mind', $pillarTags, true))>
                            <span>Mind (Tâm)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="pillar_tags[]" value="wisdom" class="rounded border-gray-300" @checked(in_array('wisdom', $pillarTags, true))>
                            <span>Wisdom (Trí)</span>
                        </label>
                    </div>
                    @error('pillar_tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Sources</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="source_tags[]" value="buddhism" class="rounded border-gray-300" @checked(in_array('buddhism', $sourceTags, true))>
                            <span>Buddhism (Phật)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="source_tags[]" value="christianity" class="rounded border-gray-300" @checked(in_array('christianity', $sourceTags, true))>
                            <span>Christianity (Chúa)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="source_tags[]" value="science" class="rounded border-gray-300" @checked(in_array('science', $sourceTags, true))>
                            <span>Science (Khoa học)</span>
                        </label>
                    </div>
                    @error('source_tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" @checked((bool) old('is_active', $video->is_active))>
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Back</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
