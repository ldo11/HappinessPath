@extends('layouts.consultant')

@section('title', 'Edit Video')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Edit Video</h2>
        <a href="{{ route('consultant.videos.index', ['locale' => app()->getLocale()]) }}" class="text-white/80 hover:text-white">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultant.videos.update', ['locale' => app()->getLocale(), 'videoId' => $video]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-white/80">Title</label>
                <input type="text" name="title" id="title" required value="{{ old('title', $video->title) }}"
                       class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                @error('title')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-white/80">URL (YouTube / Embed)</label>
                <input type="text" name="url" id="url" required value="{{ old('url', $video->url) }}"
                       class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                @error('url')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="language" class="block text-sm font-medium text-white/80">Language</label>
                <select name="language" id="language" required
                        class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <option class="text-gray-900" value="vi" @selected(old('language', $video->language ?? 'vi') === 'vi')>Vietnamese (vi)</option>
                    <option class="text-gray-900" value="en" @selected(old('language', $video->language) === 'en')>English (en)</option>
                    <option class="text-gray-900" value="de" @selected(old('language', $video->language) === 'de')>German (de)</option>
                    <option class="text-gray-900" value="kr" @selected(old('language', $video->language) === 'kr')>Korean (kr)</option>
                </select>
                @error('language')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            @php
                $pillarTags = (array) old('pillar_tags', $video->pillar_tags ?? []);
                $sourceTags = (array) old('source_tags', $video->source_tags ?? []);
            @endphp

            <div>
                <label class="block text-sm font-medium text-white/80">Pillars</label>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="pillar_tags[]" value="body" class="rounded border-white/20 bg-transparent" @checked(in_array('body', $pillarTags, true))>
                        <span>Body</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="pillar_tags[]" value="mind" class="rounded border-white/20 bg-transparent" @checked(in_array('mind', $pillarTags, true))>
                        <span>Mind</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="pillar_tags[]" value="wisdom" class="rounded border-white/20 bg-transparent" @checked(in_array('wisdom', $pillarTags, true))>
                        <span>Wisdom</span>
                    </label>
                </div>
                @error('pillar_tags')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-white/80">Sources</label>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="source_tags[]" value="buddhism" class="rounded border-white/20 bg-transparent" @checked(in_array('buddhism', $sourceTags, true))>
                        <span>Buddhism</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="source_tags[]" value="christianity" class="rounded border-white/20 bg-transparent" @checked(in_array('christianity', $sourceTags, true))>
                        <span>Christianity</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="source_tags[]" value="science" class="rounded border-white/20 bg-transparent" @checked(in_array('science', $sourceTags, true))>
                        <span>Science</span>
                    </label>
                </div>
                @error('source_tags')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-emerald-500 focus:ring-emerald-400 border-white/20 rounded bg-transparent" @checked((bool) old('is_active', $video->is_active))>
                <label for="is_active" class="ml-2 block text-sm text-white/80">Active</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('consultant.videos.index', ['locale' => app()->getLocale()]) }}" class="px-4 py-2 rounded-xl border border-white/15 text-white/80 hover:text-white hover:bg-white/5">Back</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
