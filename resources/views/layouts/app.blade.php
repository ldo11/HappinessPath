<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Con Đường Hạnh Phúc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .spiritual-font {
            font-family: 'Merriweather', serif;
        }
        .body-font {
            font-family: 'Nunito', sans-serif;
        }
        .hero-bg {
            background-image: url('/images/home-bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .emerald-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .emerald-gradient:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Responsive Typography */
        .text-responsive-xs { font-size: 0.75rem; }
        .text-responsive-sm { font-size: 0.875rem; }
        .text-responsive-base { font-size: 1rem; }
        .text-responsive-lg { font-size: 1.125rem; }
        .text-responsive-xl { font-size: 1.25rem; }
        .text-responsive-2xl { font-size: 1.5rem; }
        .text-responsive-3xl { font-size: 1.875rem; }
        .text-responsive-4xl { font-size: 2.25rem; }
        .text-responsive-5xl { font-size: 3rem; }
        
        @media (min-width: 640px) {
            .text-responsive-xs { font-size: 0.75rem; }
            .text-responsive-sm { font-size: 0.875rem; }
            .text-responsive-base { font-size: 1rem; }
            .text-responsive-lg { font-size: 1.125rem; }
            .text-responsive-xl { font-size: 1.25rem; }
            .text-responsive-2xl { font-size: 1.5rem; }
            .text-responsive-3xl { font-size: 1.875rem; }
            .text-responsive-4xl { font-size: 2.25rem; }
            .text-responsive-5xl { font-size: 3rem; }
        }
        
        @media (min-width: 768px) {
            .text-responsive-xs { font-size: 0.75rem; }
            .text-responsive-sm { font-size: 0.875rem; }
            .text-responsive-base { font-size: 1rem; }
            .text-responsive-lg { font-size: 1.125rem; }
            .text-responsive-xl { font-size: 1.25rem; }
            .text-responsive-2xl { font-size: 1.5rem; }
            .text-responsive-3xl { font-size: 2rem; }
            .text-responsive-4xl { font-size: 2.5rem; }
            .text-responsive-5xl { font-size: 3.5rem; }
        }
        
        @media (min-width: 1024px) {
            .text-responsive-xs { font-size: 0.75rem; }
            .text-responsive-sm { font-size: 0.875rem; }
            .text-responsive-base { font-size: 1rem; }
            .text-responsive-lg { font-size: 1.125rem; }
            .text-responsive-xl { font-size: 1.25rem; }
            .text-responsive-2xl { font-size: 1.5rem; }
            .text-responsive-3xl { font-size: 2.25rem; }
            .text-responsive-4xl { font-size: 3rem; }
            .text-responsive-5xl { font-size: 4rem; }
        }
        
        /* Mobile responsiveness improvements */
        @media (max-width: 768px) {
            .hero-bg {
                background-attachment: scroll;
            }
            
            .glassmorphism {
                background: rgba(255, 255, 255, 0.15);
            }
            
            .mobile-menu-hidden {
                display: none;
            }
            
            .mobile-menu-show {
                display: block;
            }
        }
    </style>
</head>
<body class="body-font antialiased">
    <!-- Hero Background with Dark Overlay -->
    <div class="hero-bg min-h-screen relative">
        <div class="absolute inset-0 bg-slate-900/80"></div>
        
        <!-- Navigation -->
        <nav class="fixed top-0 inset-x-0 z-20 glassmorphism border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-emerald-600/20 backdrop-blur-sm rounded-full flex items-center justify-center mr-2 md:mr-3 border border-emerald-400/30">
                                <i class="fas fa-route text-emerald-400 text-sm md:text-lg"></i>
                            </div>
                            <span class="font-bold text-lg md:text-xl text-white spiritual-font hidden sm:block">Con Đường Hạnh Phúc</span>
                            <span class="font-bold text-lg md:text-xl text-white spiritual-font sm:hidden">Hạnh Phúc</span>
                        </div>
                    </div>
                    
                    <!-- Right Side Navigation -->
                    <div class="flex items-center gap-2 md:gap-6 ml-auto">
                        <!-- Mobile Menu Toggle -->
                        <button onclick="toggleMobileMenu()" class="md:hidden text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button onclick="toggleProfileDropdown()" class="flex items-center text-white hover:text-white px-2 md:px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <div class="w-8 h-8 bg-emerald-600/30 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-emerald-300 text-sm"></i>
                                </div>
                                <span class="hidden md:block">{{ __('ui.hi_name', ['name' => Auth::user()->name]) }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            
                            <!-- Profile Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 glassmorphism rounded-lg shadow-lg z-50">
                                <div class="py-1">
                                    <a href="{{ route('user.profile.settings.edit') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>{{ __('menu.profile') }}
                                    </a>
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'translator')
                                        <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'translator.dashboard') }}" 
                                           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-2"></i>{{ __('ui.role_panel', ['role' => ucfirst(Auth::user()->role)]) }}
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i>{{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 pt-20">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:pl-72 pb-24 lg:pb-8">
                @include('layouts.sidebar')
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Flash Messages -->
    @if(session('status'))
        <div class="fixed top-20 right-4 z-50 glassmorphism rounded-lg px-6 py-4 text-white max-w-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-400 mr-3"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="fixed top-20 right-4 z-50 glassmorphism rounded-lg px-6 py-4 text-white max-w-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-400 mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-20 right-4 z-50 glassmorphism rounded-lg px-6 py-4 text-red-200 max-w-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="fixed top-20 right-4 z-50 glassmorphism rounded-lg px-6 py-4 text-red-200 max-w-sm">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
                <div>
                    <p class="font-semibold mb-1">{{ __('validation.please_fix_errors') }}</p>
                    <ul class="text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Scripts -->
    <script>
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                return;
            }

            const originalFetch = window.fetch;
            window.fetch = function (input, init) {
                init = init || {};
                const method = (init.method || 'GET').toUpperCase();
                if (method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS') {
                    init.headers = init.headers || {};
                    if (init.headers instanceof Headers) {
                        if (!init.headers.has('X-CSRF-TOKEN')) {
                            init.headers.set('X-CSRF-TOKEN', token);
                        }
                    } else {
                        if (!('X-CSRF-TOKEN' in init.headers)) {
                            init.headers['X-CSRF-TOKEN'] = token;
                        }
                    }
                }

                return originalFetch(input, init);
            };
        })();

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            
            dropdown.classList.toggle('hidden');
        }
        
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const profileDropdown = document.getElementById('profileDropdown');
            
            profileDropdown.classList.add('hidden');
            mobileMenu.classList.toggle('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.getElementById('profileDropdown');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (!event.target.closest('.relative') && !event.target.closest('button')) {
                profileDropdown.classList.add('hidden');
            }
            
            // Close mobile menu when clicking outside
            if (!event.target.closest('#mobileMenu') && !event.target.closest('button[onclick="toggleMobileMenu()"]')) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Locale switching is handled via Profile Settings for authenticated users.
        
        // Geo-Location Logic for Guests
        document.addEventListener('DOMContentLoaded', () => {
            const isGuest = @json(auth()->guest());
            const geoChecked = localStorage.getItem('geo_locale_checked');

            if (isGuest && !geoChecked) {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            localStorage.setItem('geo_locale_checked', 'true');
                            try {
                                const response = await fetch('{{ route('api.detect-locale') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        latitude: position.coords.latitude,
                                        longitude: position.coords.longitude
                                    })
                                });

                                if (response.ok) {
                                    const data = await response.json();
                                    const detectedLocale = data.locale;
                                    const currentLocale = '{{ app()->getLocale() }}';

                                    if (detectedLocale !== currentLocale) {
                                        // Construct new URL with detected locale
                                        const path = window.location.pathname;
                                        const parts = path.split('/');
                                        // parts[0] is empty, parts[1] is locale (en, vi, etc.)
                                        if (['en', 'vi', 'de', 'kr'].includes(parts[1])) {
                                            parts[1] = detectedLocale;
                                            const newPath = parts.join('/');
                                            window.location.href = newPath + window.location.search;
                                        } else {
                                            window.location.reload();
                                        }
                                    }
                                }
                            } catch (error) {
                                console.error('Geo-locale error:', error);
                            }
                        },
                        (error) => {
                            console.log('Geo-location denied or error:', error);
                            localStorage.setItem('geo_locale_checked', 'true');
                        }
                    );
                } else {
                    localStorage.setItem('geo_locale_checked', 'true');
                }
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
