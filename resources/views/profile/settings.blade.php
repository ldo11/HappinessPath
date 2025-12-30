@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white spiritual-font">Profile Settings</h1>
            <p class="text-white/70 text-sm mt-1">Cập nhật tuỳ chọn và quyền riêng tư của bạn.</p>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg mb-6">
        <h2 class="text-lg font-semibold text-white mb-2">Quick Links</h2>
        <a href="{{ route('settings.assessment') }}" class="text-sm text-emerald-300 hover:text-emerald-200 underline">Take assessment</a>
    </div>

    <form class="space-y-6" method="POST" action="{{ route('profile.settings.update') }}">
        @csrf

        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-white mb-4">Preferences</h2>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-200" for="language">Language</label>
                <select id="language" name="language"
                        class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0">
                    <option class="text-gray-900" value="vi" @selected(old('language', $user->language ?? $user->locale ?? 'vi') === 'vi')>Vietnamese (VI)</option>
                    <option class="text-gray-900" value="en" @selected(old('language', $user->language ?? $user->locale ?? 'vi') === 'en')>English (EN)</option>
                    <option class="text-gray-900" value="de" @selected(old('language', $user->language ?? $user->locale ?? 'vi') === 'de')>German (DE)</option>
                    <option class="text-gray-900" value="kr" @selected(old('language', $user->language ?? $user->locale ?? 'vi') === 'kr')>Korean (KR)</option>
                </select>
                @error('language')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-200" for="spiritual_preference">Spiritual preference</label>
                <select id="spiritual_preference" name="spiritual_preference"
                        class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0">
                    <option class="text-gray-900" value="buddhism" @selected(old('spiritual_preference', $user->spiritual_preference) === 'buddhism')>Buddhism</option>
                    <option class="text-gray-900" value="christianity" @selected(old('spiritual_preference', $user->spiritual_preference) === 'christianity')>Christianity</option>
                    <option class="text-gray-900" value="secular" @selected(old('spiritual_preference', $user->spiritual_preference) === 'secular')>Secular</option>
                </select>
                @error('spiritual_preference')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-200" for="religion">Religion (for filtering content)</label>
                <select id="religion" name="religion"
                        class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0">
                    <option class="text-gray-900" value="none" @selected(old('religion', $user->religion ?? 'none') === 'none')>None</option>
                    <option class="text-gray-900" value="buddhism" @selected(old('religion', $user->religion ?? 'none') === 'buddhism')>Buddhism</option>
                    <option class="text-gray-900" value="christianity" @selected(old('religion', $user->religion ?? 'none') === 'christianity')>Christianity</option>
                    <option class="text-gray-900" value="science" @selected(old('religion', $user->religion ?? 'none') === 'science')>Science</option>
                </select>
                @error('religion')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-white mb-4">Privacy</h2>
            <div>
                <input type="hidden" name="geo_privacy" value="0">
                <label class="inline-flex items-center gap-3">
                    <input type="checkbox" name="geo_privacy" value="1" class="rounded bg-transparent border-gray-500 text-emerald-500 focus:ring-emerald-400" @checked((bool) old('geo_privacy', $user->geo_privacy))>
                    <span class="text-sm text-gray-200">Hide my location (geo privacy)</span>
                </label>
                @error('geo_privacy')
                    <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg">
            <div class="flex items-center justify-end">
                <button type="submit" class="emerald-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
