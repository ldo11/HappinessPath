@extends('layouts.guest')

@section('title', __('auth.register'))
@section('auth-subtitle', __('auth.start_healing_journey'))

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf

    <!-- Name Input -->
    <div>
        <label for="name" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-user mr-2"></i>{{ __('auth.name') }}
        </label>
        <input id="name" 
               name="name" 
               value="{{ old('name') }}" 
               class="glass-input w-full px-3 sm:px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300 text-base" 
               placeholder="{{ __('validation.name_required') }}"
               required>
        @error('name')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Invalid name') }}
            </p>
        @enderror
    </div>

    <!-- Email Input -->
    <div>
        <label for="email" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-envelope mr-2"></i>{{ __('auth.email') }}
        </label>
        <input id="email" 
               type="email" 
               name="email" 
               value="{{ old('email') }}" 
               class="glass-input w-full px-3 sm:px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300 text-base" 
               placeholder="{{ __('validation.email') }}"
               required>
        @error('email')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Invalid email') }}
            </p>
        @enderror
    </div>

    <!-- City Input -->
    <div>
        <label for="city" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-map-marker-alt mr-2"></i>{{ __('auth.city') }}
        </label>
        <input id="city" 
               name="city" 
               value="{{ old('city') }}" 
               class="glass-input w-full px-3 sm:px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300 text-base" 
               placeholder="{{ __('validation.city_required') }}"
               required>
        @error('city')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Invalid city') }}
            </p>
        @enderror
    </div>

    <!-- Spiritual Preference -->
    <div>
        <label for="spiritual_preference" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-spa mr-2"></i>{{ __('auth.spiritual_preference') }}
        </label>
        <select id="spiritual_preference" 
                name="spiritual_preference" 
                class="glass-input w-full px-3 sm:px-4 py-3 text-white rounded-lg focus:outline-none transition-all duration-300 bg-emerald-600/20 border-emerald-400/30 text-base"
                required>
            <option value="" class="bg-slate-800">{{ __('validation.spiritual_preference_required') }}</option>
            <option value="buddhism" @selected(old('spiritual_preference') === 'buddhism') class="bg-slate-800">{{ __('auth.buddhism') }}</option>
            <option value="christianity" @selected(old('spiritual_preference') === 'christianity') class="bg-slate-800">{{ __('auth.christianity') }}</option>
            <option value="secular" @selected(old('spiritual_preference') === 'secular') class="bg-slate-800">{{ __('auth.secular') }}</option>
        </select>
        @error('spiritual_preference')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Please select a spiritual preference') }}
            </p>
        @enderror
    </div>

    <!-- Password Input -->
    <div>
        <label for="password" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-lock mr-2"></i>{{ __('auth.password') }}
        </label>
        <input id="password" 
               type="password" 
               name="password" 
               class="glass-input w-full px-3 sm:px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300 text-base" 
               placeholder="{{ __('validation.password_min') }}"
               required>
        @error('password')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Invalid password') }}
            </p>
        @enderror
    </div>

    <!-- Password Confirmation -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-lock mr-2"></i>{{ __('auth.confirm_password') }}
        </label>
        <input id="password_confirmation" 
               type="password" 
               name="password_confirmation" 
               class="glass-input w-full px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300" 
               placeholder="{{ __('auth.confirm_password') }}"
               required>
    </div>

    <!-- Privacy Settings -->
    <div class="space-y-3">
        <label class="flex items-center">
            <input type="checkbox" 
                   name="geo_privacy" 
                   value="1" 
                   class="rounded border-emerald-400 bg-emerald-600/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-emerald-500/20"
                   @checked(old('geo_privacy'))>
            <span class="ml-2 text-sm text-emerald-200">{{ __('auth.geo_privacy') }}</span>
        </label>
        <p class="text-xs text-emerald-300">
            <i class="fas fa-info-circle mr-1"></i>
            {{ __('auth.geo_privacy_desc') }}
        </p>
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button type="submit" 
                class="w-full emerald-gradient text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
            <i class="fas fa-user-plus mr-2"></i>
            {{ __('auth.create_account') }}
        </button>
    </div>

    <!-- Welcome Message -->
    <div class="border-t border-emerald-400/20 pt-6">
        <div class="text-center space-y-2">
            <p class="text-emerald-200 text-sm">
                <i class="fas fa-heart mr-2 text-red-400"></i>
                {{ __('messages.welcome') }} {{ __('common.to') }} Con Đường Hạnh Phúc
            </p>
            <p class="text-emerald-300 text-xs">
                {{ __('auth.start_healing_journey') }}...
            </p>
        </div>
    </div>
</form>
@endsection
