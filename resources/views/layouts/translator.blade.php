<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Translator Portal') - Happiness Path</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-900 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold text-indigo-200">
                    <i class="fas fa-language mr-2"></i>Translator Portal
                </h1>
            </div>
            <nav class="mt-8">
                <a href="{{ route('translator.dashboard') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.dashboard') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                </a>
                <a href="{{ route('translator.translations.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.translations.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-language mr-3"></i>Translation Review
                </a>
                <a href="{{ route('translator.app-translations.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.app-translations.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-file-export mr-3"></i>App Translations
                </a>
                <a href="{{ route('translator.users.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.users.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-users mr-3"></i>Users
                </a>
                <div class="border-t border-indigo-700 mt-4 pt-4">
                    <a href="{{ route('home') }}" class="block px-4 py-3 hover:bg-indigo-800">
                        <i class="fas fa-arrow-left mr-3"></i>Back to Site
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button onclick="toggleLanguageDropdown()" class="flex items-center text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <span class="text-lg mr-2">{{ app()->getLocale() === 'vi' ? '🇻🇳' : '🇺🇸' }}</span>
                                <span>{{ app()->getLocale() === 'vi' ? 'Tiếng Việt' : 'English' }}</span>
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div id="languageDropdown" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg z-50 border">
                                <div class="py-1">
                                    <a href="#" data-locale-switch="vi" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <span class="text-xl mr-3">🇻🇳</span>
                                        <div class="font-medium">Tiếng Việt</div>
                                        @if(app()->getLocale() === 'vi')
                                            <i class="fas fa-check text-indigo-600 ml-auto"></i>
                                        @endif
                                    </a>
                                    <a href="#" data-locale-switch="en" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <span class="text-xl mr-3">🇺🇸</span>
                                        <div class="font-medium">English</div>
                                        @if(app()->getLocale() === 'en')
                                            <i class="fas fa-check text-indigo-600 ml-auto"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-user mr-2"></i>Hi {{ Auth::user()->name }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')

    <script>
        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('languageDropdown');
            dropdown.classList.toggle('hidden');
        }

        function switchLocale(locale) {
            if (locale !== 'en' && locale !== 'vi') {
                return;
            }

            const parts = window.location.pathname.split('/').filter(Boolean);
            if (parts.length > 0 && (parts[0] === 'en' || parts[0] === 'vi')) {
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
