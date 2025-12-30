@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Edit User: {{ $user->name ?? '' }}</h3>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $user->name ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                           value="{{ old('email', $user->email ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="translator" {{ old('role', $user->role) == 'translator' ? 'selected' : '' }}>Translator</option>
                        <option value="consultant" {{ old('role', $user->role) == 'consultant' ? 'selected' : '' }}>Consultant</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city"
                           value="{{ old('city', $user->city ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verification Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                    <div class="mt-2 flex items-center justify-between gap-4">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                Unverified
                            </span>
                            <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md text-white hover:bg-yellow-700">
                                    Force Verify Manually
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Spiritual Preference -->
                <div>
                    <label for="spiritual_preference" class="block text-sm font-medium text-gray-700">Spiritual Preference</label>
                    <select name="spiritual_preference" id="spiritual_preference"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select preference</option>
                        <option value="buddhism" {{ old('spiritual_preference', $user->spiritual_preference) == 'buddhism' ? 'selected' : '' }}>Buddhism</option>
                        <option value="christianity" {{ old('spiritual_preference', $user->spiritual_preference) == 'christianity' ? 'selected' : '' }}>Christianity</option>
                        <option value="secular" {{ old('spiritual_preference', $user->spiritual_preference) == 'secular' ? 'selected' : '' }}>Secular</option>
                    </select>
                    @error('spiritual_preference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Geo Privacy -->
                <div class="flex items-center">
                    <input type="checkbox" name="geo_privacy" id="geo_privacy" value="1"
                           {{ old('geo_privacy', $user->geo_privacy) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="geo_privacy" class="ml-2 block text-sm text-gray-900">
                        Enable geo privacy (hide location from other users)
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                @if(($user->role ?? null) === 'user')
                    <form method="POST" action="{{ route('admin.users.reset-assessment', $user) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-600 border border-transparent rounded-md text-white hover:bg-amber-700" onclick="return confirm('Reset this user\'s assessment?')">
                            Reset Assessment
                        </button>
                    </form>
                @endif
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
