@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white spiritual-font">Profile Settings</h1>
            <p class="text-white/70 text-sm mt-1">Update your profile information.</p>
        </div>
    </div>

    <form class="space-y-6" method="POST" action="{{ route('user.profile.settings.update', ['locale' => app()->getLocale()]) }}">
        @csrf

        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-white mb-4">Profile</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-200" for="name">Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                           class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200" for="location">Location</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $user->location) }}"
                           class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0" />
                    @error('location')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200" for="display_language">Display Language</label>
                    <select id="display_language" name="display_language"
                            class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0">
                        <option value="en" class="text-black" @selected(old('display_language', $user->display_language) === 'en')>English</option>
                        <option value="vi" class="text-black" @selected(old('display_language', $user->display_language) === 'vi')>Vietnamese</option>
                        <option value="de" class="text-black" @selected(old('display_language', $user->display_language) === 'de')>German</option>
                        <option value="kr" class="text-black" @selected(old('display_language', $user->display_language) === 'kr')>Korean</option>
                    </select>
                    @error('display_language')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-200" for="introduction">Introduction</label>
                    <textarea id="introduction" name="introduction" rows="3"
                              class="mt-2 block w-full rounded-lg bg-transparent text-white border border-gray-500 focus:border-emerald-400 focus:ring-0">{{ old('introduction', $user->introduction) }}</textarea>
                    @error('introduction')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        @if($user->hasRole('consultant'))
            <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg">
                <h2 class="text-lg font-semibold text-white mb-4">Consultant Settings</h2>

                <div>
                    <input type="hidden" name="is_available" value="0">
                    <label class="inline-flex items-center gap-3">
                        <input type="checkbox" name="is_available" value="1" class="rounded bg-transparent border-gray-500 text-emerald-500 focus:ring-emerald-400" @checked((bool) old('is_available', $user->is_available))>
                        <span class="text-sm text-gray-200">Accepting new consultations</span>
                    </label>
                    @error('is_available')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-200" for="consultant_pain_points">My Skills</label>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($painPoints as $pp)
                            <label class="inline-flex items-center gap-3 rounded-lg bg-white/5 border border-white/10 px-4 py-3">
                                <input type="checkbox" name="consultant_pain_points[]" value="{{ $pp->id }}"
                                       class="rounded bg-transparent border-gray-500 text-emerald-500 focus:ring-emerald-400"
                                       @checked(in_array($pp->id, old('consultant_pain_points', $selectedConsultantPainPointIds ?? []), true))>
                                <span class="text-sm text-gray-200">{{ $pp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('consultant_pain_points')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                    @error('consultant_pain_points.*')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

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
