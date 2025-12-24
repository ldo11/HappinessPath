<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <nav class="relative z-20 glassmorphism border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-emerald-600/20 backdrop-blur-sm rounded-full flex items-center justify-center mr-2 md:mr-3 border border-emerald-400/30">
                                <i class="fas fa-tree text-emerald-400 text-sm md:text-lg"></i>
                            </div>
                            <span class="font-bold text-lg md:text-xl text-white spiritual-font hidden sm:block">Con Đường Hạnh Phúc</span>
                            <span class="font-bold text-lg md:text-xl text-white spiritual-font sm:hidden">Hạnh Phúc</span>
                        </div>
                    </div>
                    
                    <!-- Right Side Navigation -->
                    <div class="flex items-center space-x-2 md:space-x-6">
                        <!-- Mobile Menu Toggle -->
                        <button onclick="toggleMobileMenu()" class="md:hidden text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <!-- Language Switcher - Desktop Only -->
                        <div class="relative hidden md:block">
                            <button onclick="toggleLanguageDropdown()" class="flex items-center text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <span class="text-lg mr-2">{{ app()->getLocale() === 'vi' ? '🇻🇳' : '🇺🇸' }}</span>
                                <span class="hidden lg:block">{{ app()->getLocale() === 'vi' ? 'Tiếng Việt' : 'English' }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            
                            <!-- Language Dropdown -->
                            <div id="languageDropdown" class="hidden absolute right-0 mt-2 w-52 glassmorphism rounded-lg shadow-lg z-50">
                                <div class="py-1">
                                    <a href="{{ route('language.switch', 'vi') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇻🇳</span>
                                        <div>
                                            <div class="font-medium">Tiếng Việt</div>
                                            <div class="text-xs text-emerald-200">Vietnamese</div>
                                        </div>
                                        @if(app()->getLocale() === 'vi')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-3 text-sm text-white hover:bg-white/10 transition-colors">
                                        <span class="text-xl mr-3">🇺🇸</span>
                                        <div>
                                            <div class="font-medium">English</div>
                                            <div class="text-xs text-emerald-200">United States</div>
                                        </div>
                                        @if(app()->getLocale() === 'en')
                                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button onclick="toggleProfileDropdown()" class="flex items-center text-white hover:text-white px-2 md:px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <div class="w-8 h-8 bg-emerald-600/30 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-emerald-300 text-sm"></i>
                                </div>
                                <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            
                            <!-- Profile Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 glassmorphism rounded-lg shadow-lg z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 border-b border-white/10">
                                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-emerald-200">{{ Auth::user()->email }}</p>
                                        <p class="text-xs text-emerald-300 mt-1">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            {{ ucfirst(Auth::user()->role) }}
                                        </p>
                                    </div>
                                    
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                        <i class="fas fa-home mr-2"></i>Dashboard
                                    </a>
                                    <a href="{{ route('profile.settings.edit') }}" class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                        <i class="fas fa-user-edit mr-2"></i>Hồ sơ cá nhân
                                    </a>
                                    <a href="{{ route('profile.settings.edit') }}" class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                        <i class="fas fa-cog mr-2"></i>Cài đặt
                                    </a>
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                            <i class="fas fa-crown mr-2"></i>Quản trị viên
                                        </a>
                                    @endif
                                    
                                    @if(Auth::user()->role === 'volunteer')
                                        <a href="{{ route('volunteer.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                            <i class="fas fa-hands-helping mr-2"></i>Tình nguyện viên
                                        </a>
                                    @endif
                                    
                                    <div class="border-t border-white/10">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-300 hover:bg-white/10 transition-colors">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden relative z-20 glassmorphism border-b border-white/10">
            <div class="px-4 py-4 space-y-1">
                <!-- Main Navigation -->
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <i class="fas fa-home mr-3 text-lg"></i>
                        <span class="font-medium">{{ __('navigation.dashboard') }}</span>
                    </a>
                    <a href="{{ route('meditate') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <i class="fas fa-spa mr-3 text-lg"></i>
                        <span class="font-medium">{{ __('navigation.meditation') }}</span>
                    </a>
                    <a href="{{ route('profile.settings.edit') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <i class="fas fa-user-edit mr-3 text-lg"></i>
                        <span class="font-medium">{{ __('navigation.profile') }}</span>
                    </a>
                    <a href="{{ route('profile.settings.edit') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <i class="fas fa-cog mr-3 text-lg"></i>
                        <span class="font-medium">{{ __('navigation.settings') }}</span>
                    </a>
                </div>
                
                <!-- Language Switcher -->
                <div class="border-t border-white/10 pt-2 mt-2 space-y-1">
                    <p class="px-4 py-2 text-xs text-emerald-200 font-medium uppercase tracking-wider">{{ __('common.language') }}</p>
                    <a href="{{ route('language.switch', 'vi') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <span class="text-xl mr-3">🇻🇳</span>
                        <div>
                            <div class="font-medium">Tiếng Việt</div>
                            <div class="text-xs text-emerald-200">Vietnamese</div>
                        </div>
                        @if(app()->getLocale() === 'vi')
                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                        @endif
                    </a>
                    <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <span class="text-xl mr-3">🇺🇸</span>
                        <div>
                            <div class="font-medium">English</div>
                            <div class="text-xs text-emerald-200">United States</div>
                        </div>
                        @if(app()->getLocale() === 'en')
                            <i class="fas fa-check text-emerald-400 ml-auto"></i>
                        @endif
                    </a>
                </div>
                
                <!-- Role-specific Links -->
                @if(Auth::user()->role === 'admin')
                    <div class="border-t border-white/10 pt-2 mt-2">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                            <i class="fas fa-crown mr-3 text-lg text-yellow-400"></i>
                            <span class="font-medium">{{ __('navigation.admin') }}</span>
                        </a>
                    </div>
                @endif
                
                @if(Auth::user()->role === 'volunteer')
                    <div class="border-t border-white/10 pt-2 mt-2">
                        <a href="{{ route('volunteer.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                            <i class="fas fa-hands-helping mr-3 text-lg text-green-400"></i>
                            <span class="font-medium">{{ __('navigation.volunteer') }}</span>
                        </a>
                    </div>
                @endif
                
                <!-- Logout -->
                <div class="border-t border-white/10 pt-2 mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-red-300 hover:bg-white/10 transition-colors">
                            <i class="fas fa-sign-out-alt mr-3 text-lg"></i>
                            <span class="font-medium">{{ __('auth.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="relative z-10">
            @yield('content')
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
                    <p class="font-semibold mb-1">Vui lòng sửa các lỗi sau:</p>
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
        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('languageDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            
            profileDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }
        
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const languageDropdown = document.getElementById('languageDropdown');
            
            languageDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }
        
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const languageDropdown = document.getElementById('languageDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            
            languageDropdown.classList.add('hidden');
            profileDropdown.classList.add('hidden');
            mobileMenu.classList.toggle('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const languageDropdown = document.getElementById('languageDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (!event.target.closest('.relative') && !event.target.closest('button')) {
                languageDropdown.classList.add('hidden');
                profileDropdown.classList.add('hidden');
            }
            
            // Close mobile menu when clicking outside
            if (!event.target.closest('#mobileMenu') && !event.target.closest('button[onclick="toggleMobileMenu()"]')) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
