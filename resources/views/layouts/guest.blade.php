<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Con Đường Hạnh Phúc') - Chữa lành và Phát triển</title>
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
        .glass-input {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-bottom-color: #10b981;
            outline: none;
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
    </style>
</head>
<body class="body-font antialiased">
    <!-- Hero Background with Dark Overlay -->
    <div class="hero-bg min-h-screen relative">
        <div class="absolute inset-0 bg-slate-900/80"></div>
        
        <!-- Main Content -->
        <main class="relative z-10 min-h-screen flex items-center justify-center px-4 py-8 sm:py-12">
            <div class="w-full max-w-md">
                <!-- Logo and Title -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="flex justify-end mb-4">
                        <div class="relative">
                            <button onclick="toggleLanguageDropdown()" class="flex items-center text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors glassmorphism">
                                <span class="text-lg mr-2">
                                    {{ match(app()->getLocale()) {
                                        'vi' => '🇻🇳',
                                        'en' => '🇺🇸',
                                        'de' => '🇩🇪',
                                        'kr' => '🇰🇷',
                                        default => '🇻🇳'
                                    } }}
                                </span>
                                <span>
                                    {{ match(app()->getLocale()) {
                                        'vi' => 'Tiếng Việt',
                                        'en' => 'English',
                                        'de' => 'Deutsch',
                                        'kr' => '한국어',
                                        default => 'Tiếng Việt'
                                    } }}
                                </span>
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div id="languageDropdown" class="hidden absolute right-0 mt-2 w-52 glassmorphism rounded-lg shadow-lg z-50">
                                <div class="py-1">
                                    <a href="{{ route('language.switch', 'vi') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇻🇳</span>
                                        <div class="font-medium">Tiếng Việt</div>
                                        @if(app()->getLocale() === 'vi')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇺🇸</span>
                                        <div class="font-medium">English</div>
                                        @if(app()->getLocale() === 'en')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('language.switch', 'de') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇩🇪</span>
                                        <div class="font-medium">Deutsch</div>
                                        @if(app()->getLocale() === 'de')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('language.switch', 'kr') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇰🇷</span>
                                        <div class="font-medium">한국어</div>
                                        @if(app()->getLocale() === 'kr')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-600/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-float border border-emerald-400/30">
                        <i class="fas fa-route text-emerald-400 text-2xl sm:text-3xl"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white spiritual-font mb-2">
                        <span class="hidden sm:inline">Con Đường Hạnh Phúc</span>
                        <span class="sm:hidden">Hạnh Phúc</span>
                    </h1>
                    <p class="text-emerald-200 text-sm sm:text-base">
                        @yield('auth-subtitle', 'Chữa lành nỗi đau - Vun bồi nội lực - Khai mở trí tuệ')
                    </p>
                </div>

                <!-- Glassmorphism Card -->
                <div class="glassmorphism rounded-2xl p-6 sm:p-8 shadow-2xl">
                    @yield('content')
                </div>

                <!-- Footer Links -->
                <div class="text-center mt-6 text-emerald-200 text-sm">
                    @if(Route::has('login') && Route::has('register'))
                        @if (request()->routeIs('login'))
                            <p>Bạn chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="text-emerald-400 hover:text-emerald-300 underline transition-colors">
                                    Đăng ký ngay
                                </a>
                            </p>
                        @elseif (request()->routeIs('register'))
                            <p>Đã có tài khoản? 
                                <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 underline transition-colors">
                                    Đăng nhập
                                </a>
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Flash Messages -->
    @if(session('status'))
        <div class="fixed top-4 right-4 z-50 glassmorphism rounded-lg px-6 py-4 text-white max-w-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-400 mr-3"></i>
                <span>{{ session('status') }}</span>
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

    @yield('scripts')

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

        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('languageDropdown');
            dropdown.classList.toggle('hidden');
        }

        function switchLocale(locale) {
            if (!['vi', 'en', 'de', 'kr'].includes(locale)) {
                return;
            }

            const parts = window.location.pathname.split('/').filter(Boolean);
            if (parts.length > 0 && ['vi', 'en', 'de', 'kr'].includes(parts[0])) {
                parts[0] = locale;
            } else {
                parts.unshift(locale);
            }

            const newPath = '/' + parts.join('/');
            window.location.assign(newPath + window.location.search + window.location.hash);
        }

        document.querySelectorAll('[data-locale-switch]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const locale = el.getAttribute('data-locale-switch');
                switchLocale(locale);
            });
        });

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('languageDropdown');
            if (!dropdown) {
                return;
            }

            if (!event.target.closest('#languageDropdown') && !event.target.closest('button[onclick="toggleLanguageDropdown()"]')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
