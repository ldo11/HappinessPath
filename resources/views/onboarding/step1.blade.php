@extends('layouts.app')

@section('title', 'Join Your Happiness Path')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-green-50 via-blue-50 to-purple-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-primary-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-float">
                <i class="fas fa-route text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome to Your Path</h1>
            <p class="text-gray-600">Begin your journey to mental wellness and inner peace</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Step 1 of 3</span>
                <span class="text-sm text-gray-500">Create Account</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-500 h-2 rounded-full" style="width: 33%"></div>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('onboarding.step1.submit') }}" class="space-y-6">
                @csrf
                
                <!-- Basic Info -->
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Enter your full name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="your@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Create a strong password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <!-- Location & Preferences -->
                <div class="space-y-4 pt-4 border-t border-gray-200">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>City
                        </label>
                        <input type="text" name="city" id="city" required
                               value="{{ old('city') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Your city">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="spiritual_preference" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heart mr-1"></i>Spiritual Path
                        </label>
                        <select name="spiritual_preference" id="spiritual_preference" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="">Choose your spiritual preference</option>
                            <option value="buddhism" {{ old('spiritual_preference') == 'buddhism' ? 'selected' : '' }}>
                                🕉️ Buddhism
                            </option>
                            <option value="christianity" {{ old('spiritual_preference') == 'christianity' ? 'selected' : '' }}>
                                ✝️ Christianity
                            </option>
                            <option value="secular" {{ old('spiritual_preference') == 'secular' ? 'selected' : '' }}>
                                🌱 Secular/Mindfulness
                            </option>
                        </select>
                        @error('spiritual_preference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_pain_level" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heartbeat mr-1"></i>Current Stress Level (1-10)
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="range" name="start_pain_level" id="start_pain_level" 
                                   min="1" max="10" value="5" required
                                   class="flex-1"
                                   oninput="updatePainLevel(this.value)">
                            <span id="painLevelDisplay" class="w-8 text-center font-medium text-gray-700">5</span>
                        </div>
                        <input type="hidden" name="start_pain_level" id="start_pain_level_hidden" value="5">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Calm</span>
                            <span>Very Stressed</span>
                        </div>
                        @error('start_pain_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="geo_privacy" id="geo_privacy" value="1"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="geo_privacy" class="ml-2 block text-sm text-gray-700">
                            Keep my location private from other users
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        Continue to Assessment
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function updatePainLevel(value) {
    document.getElementById('painLevelDisplay').textContent = value;
    document.getElementById('start_pain_level_hidden').value = value;
    
    // Update color based on level
    const display = document.getElementById('painLevelDisplay');
    if (value <= 3) {
        display.className = 'w-8 text-center font-medium text-green-600';
    } else if (value <= 7) {
        display.className = 'w-8 text-center font-medium text-yellow-600';
    } else {
        display.className = 'w-8 text-center font-medium text-red-600';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePainLevel(5);
});
</script>
@endsection
@endsection
