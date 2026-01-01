@extends('layouts.guest')

@section('title', __('auth.login'))
@section('auth-subtitle', __('auth.welcome_back'))

@section('content')
<form method="POST" action="{{ url(app()->getLocale() . '/login') }}" class="space-y-6">
    @csrf

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

    <!-- Password Input -->
    <div>
        <label for="password" class="block text-sm font-medium text-emerald-200 mb-2">
            <i class="fas fa-lock mr-2"></i>{{ __('auth.password') }}
        </label>
        <input id="password" 
               type="password" 
               name="password" 
               class="glass-input w-full px-3 sm:px-4 py-3 text-white placeholder-emerald-200/50 rounded-lg focus:outline-none transition-all duration-300 text-base" 
               placeholder="{{ __('validation.password') }}"
               required>
        @error('password')
            <p class="mt-2 text-sm text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ is_string($message) ? $message : (is_array($message) ? implode(' ', $message) : 'Invalid password') }}
            </p>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
        <label class="flex items-center">
            <input type="checkbox" 
                   name="remember" 
                   value="1" 
                   class="rounded border-emerald-400 bg-emerald-600/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-emerald-500/20"
                   @checked(old('remember'))>
            <span class="ml-2 text-sm text-emerald-200">{{ __('auth.remember_me') }}</span>
        </label>
        
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" 
               class="text-sm text-emerald-300 hover:text-emerald-200 transition-colors">
                {{ __('auth.forgot_password') }}
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button type="submit" 
                class="w-full emerald-gradient text-white font-semibold py-3 sm:py-4 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg text-base sm:text-lg">
            <i class="fas fa-sign-in-alt mr-2"></i>
            {{ __('auth.login') }}
        </button>
    </div>

    <!-- Test Login Options -->
    <div class="border-t border-emerald-400/20 pt-6">
        <p class="text-center text-emerald-200 text-sm mb-4">
            <i class="fas fa-flask mr-2"></i>{{ __('auth.login_quick_test') }}
        </p>
        
        <div class="space-y-2">
            <button type="button" 
                    onclick="fillTestCredentials('admin@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-crown mr-2 text-yellow-400"></i>
                {{ __('auth.login_admin') }}
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('consultant@example.com', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user-md mr-2 text-emerald-300"></i>
                Consultant
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('consultant1@test.com', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user-md mr-2 text-emerald-300"></i>
                Consultant 1
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('consultant2@test.com', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user-md mr-2 text-emerald-300"></i>
                Consultant 2
            </button>
            
            <button type="button" 
                    onclick="fillTestCredentials('user@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user mr-2 text-blue-400"></i>
                {{ __('auth.login_user') }}
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('translator@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-language mr-2 text-indigo-300"></i>
                Translator
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('user_vi@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user mr-2 text-red-400"></i>
                User (Vietnamese)
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('user_de@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user mr-2 text-yellow-400"></i>
                User (German)
            </button>

            <button type="button" 
                    onclick="fillTestCredentials('user_kr@happiness.test', 'password')"
                    class="w-full glassmorphism text-white py-2 px-4 rounded-lg text-sm hover:bg-white/20 transition-all duration-200 border border-emerald-400/30">
                <i class="fas fa-user mr-2 text-purple-400"></i>
                User (Korean)
            </button>
        </div>
    </div>
</form>

@section('scripts')
<script>
function fillTestCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    
    // Add a subtle animation
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    emailInput.classList.add('ring-2', 'ring-emerald-400');
    passwordInput.classList.add('ring-2', 'ring-emerald-400');
    
    setTimeout(() => {
        emailInput.classList.remove('ring-2', 'ring-emerald-400');
        passwordInput.classList.remove('ring-2', 'ring-emerald-400');
    }, 1000);
}
</script>
@endsection
@endsection
