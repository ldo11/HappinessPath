<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Happiness Path') - Path to Happiness</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#22c55e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Happiness Path">
    <meta name="application-name" content="Happiness Path">
    <meta name="description" content="A compassionate mobile app for mental healing, meditation, and personal growth">
    <meta name="keywords" content="meditation, mindfulness, mental health, wellness, happiness, self-care">
    <meta name="author" content="Happiness Path">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    
    <!-- Fonts and Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        heart: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        grit: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#ca8a04',
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12',
                        },
                        wisdom: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'bounce-slow': 'bounce 2s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Mobile-first responsive utilities */
        .mobile-container {
            max-width: 100vw;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom scrollbar for mobile */
        ::-webkit-scrollbar {
            width: 4px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }
        
        /* Touch-friendly tap targets */
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Safe area padding for mobile */
        .safe-area-top {
            padding-top: env(safe-area-inset-top);
        }
        
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Tree health bar animation */
        .health-bar {
            transition: width 0.5s ease-in-out;
        }
        
        /* Meditation timer styles */
        .timer-circle {
            stroke-dasharray: 754;
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear;
        }
        
        /* Smooth transitions */
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Prevent text selection on mobile */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="mobile-container bg-gradient-to-br from-green-50 to-blue-50 safe-area-top safe-area-bottom">
    <!-- Mobile Navigation (if logged in) -->
    @auth
        @if(!request()->routeIs('onboarding.*') && !request()->routeIs('meditate'))
            <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 safe-area-top">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-tree text-white text-sm"></i>
                        </div>
                        <span class="font-semibold text-gray-900">Happiness Path</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if(Auth::user()->userTree)
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->userTree->exp }}</span>
                            </div>
                        @endif
                        <button onclick="toggleMobileMenu()" class="touch-target text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div id="mobileMenu" class="hidden border-t border-gray-200">
                    <div class="px-4 py-2 space-y-1">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('meditate') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-spa mr-2"></i>Meditate
                        </a>
                        <a href="{{ route('profile.settings.edit') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'translator')
                            <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'translator.dashboard') }}" 
                               class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>{{ ucfirst(Auth::user()->role) }} Panel
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        @endif
    @endauth

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation (if logged in and not onboarding) -->
    @auth
        @if(!request()->routeIs('onboarding.*') && !request()->routeIs('meditate'))
            <nav class="bg-white border-t border-gray-200 sticky bottom-0 z-40 safe-area-bottom">
                <div class="flex justify-around py-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex flex-col items-center p-2 touch-target {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-600' }}">
                        <i class="fas fa-home text-xl mb-1"></i>
                        <span class="text-xs">Home</span>
                    </a>
                    <a href="{{ route('meditate') }}" 
                       class="flex flex-col items-center p-2 touch-target {{ request()->routeIs('meditate') ? 'text-primary-600' : 'text-gray-600' }}">
                        <i class="fas fa-spa text-xl mb-1"></i>
                        <span class="text-xs">Meditate</span>
                    </a>
                    <button onclick="openDonateModal()" 
                            class="flex flex-col items-center p-2 touch-target text-gray-600">
                        <i class="fas fa-heart text-xl mb-1"></i>
                        <span class="text-xs">Give</span>
                    </button>
                    <a href="{{ route('profile.settings.edit') }}" 
                       class="flex flex-col items-center p-2 touch-target {{ request()->routeIs('profile.settings.*') ? 'text-primary-600' : 'text-gray-600' }}">
                        <i class="fas fa-user text-xl mb-1"></i>
                        <span class="text-xs">Profile</span>
                    </a>
                </div>
            </nav>
        @endif
    @endauth

    <!-- Scripts -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        function openDonateModal() {
            // Will be implemented later
            console.log('Donate modal to be implemented');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!menuButton && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
        
        // Prevent zoom on double tap for mobile
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New version available, show update notification
                                    if (confirm('A new version of Happiness Path is available. Would you like to update?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button or banner (optional)
            showInstallButton();
        });

        function showInstallButton() {
            // You can customize this to show an install banner
            const installBtn = document.createElement('button');
            installBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Install App';
            installBtn.className = 'fixed bottom-20 right-4 bg-primary-600 text-white px-4 py-2 rounded-full shadow-lg z-50';
            installBtn.onclick = function() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(choiceResult) {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                        installBtn.remove();
                    });
                }
            };
            document.body.appendChild(installBtn);
        }

        // Handle app installed event
        window.addEventListener('appinstalled', function(evt) {
            console.log('Happiness Path was installed');
            // Track installation or show success message
        });

        // Request notification permission for PWA
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                }
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>
