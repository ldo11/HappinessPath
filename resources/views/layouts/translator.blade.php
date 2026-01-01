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
                <a href="{{ route('translator.ui-matrix.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.ui-matrix.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-language mr-3"></i>UI Matrix
                </a>
                <a href="{{ route('translator.assessments.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.assessments.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-list-check mr-3"></i>Assessments
                </a>
                <a href="{{ route('translator.daily-missions.index') }}" class="block px-4 py-3 hover:bg-indigo-800 {{ request()->routeIs('translator.daily-missions.*') ? 'bg-indigo-800 border-l-4 border-indigo-200' : '' }}">
                    <i class="fas fa-bullseye mr-3"></i>Daily Missions
                </a>
                <div class="border-t border-indigo-700 mt-4 pt-4">
                    <a href="{{ route('user.home', ['locale' => session('locale') ?? config('app.locale', 'en')]) }}" class="block px-4 py-3 hover:bg-indigo-800">
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
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-user mr-2"></i>Hi {{ Auth::user()->name }}
                        </span>
                        <form action="{{ route('logout', ['locale' => session('locale') ?? config('app.locale', 'en')]) }}" method="POST" class="inline">
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

    @stack('scripts')
</body>
</html>
