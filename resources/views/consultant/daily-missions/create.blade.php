@extends('layouts.app')

@section('title', 'Create Daily Mission')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Create Daily Mission</h2>
        <a href="{{ route('consultant.daily-missions.index', ['locale' => app()->getLocale()]) }}" class="text-white/80 hover:text-white">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultant.daily-missions.store', ['locale' => app()->getLocale()]) }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-white/80">Title</label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                       class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                @error('title')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-white/80">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="points" class="block text-sm font-medium text-white/80">Points</label>
                <input type="number" name="points" id="points" required min="0" value="{{ old('points', 0) }}"
                       class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                @error('points')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-white/80">Pillars</label>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <input type="hidden" name="is_body" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="is_body" value="1" class="rounded border-white/20 bg-transparent" @checked((bool) old('is_body'))>
                        <span>Body</span>
                    </label>

                    <input type="hidden" name="is_mind" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="is_mind" value="1" class="rounded border-white/20 bg-transparent" @checked((bool) old('is_mind'))>
                        <span>Mind</span>
                    </label>

                    <input type="hidden" name="is_wisdom" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-white/80">
                        <input type="checkbox" name="is_wisdom" value="1" class="rounded border-white/20 bg-transparent" @checked((bool) old('is_wisdom'))>
                        <span>Wisdom</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
