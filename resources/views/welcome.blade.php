<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Con Đường Hạnh Phúc - Chữa lành và Phát triển</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .serif-heading {
            font-family: 'Merriweather', serif;
        }
        .sans-serif-body {
            font-family: 'Inter', sans-serif;
        }
        .hero-bg {
            background-image: url('/images/home-bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <!-- Hero Section with Background -->
    <div class="hero-bg h-screen relative">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
        
        <!-- Navigation -->
        <nav class="relative z-10 bg-white/10 backdrop-blur-sm border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-tree text-white text-sm"></i>
                            </div>
                            <span class="font-bold text-xl text-white serif-heading">Con Đường Hạnh Phúc</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @guest
                            <a href="{{ route('login') }}" class="text-white/80 hover:text-white px-3 py-2 rounded-md text-sm font-medium sans-serif-body transition-colors">
                                Đăng nhập
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium sans-serif-body transition-colors">
                                </a>
                            @endif
                        @else
                            <div class="flex items-center space-x-3">
                                <span class="text-white/80 text-sm sans-serif-body">
                                    <i class="fas fa-user-circle mr-1"></i>
                                    Xin chào, {{ Auth::user()->name }}!
                                </span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium sans-serif-body transition-colors">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Centered Hero Content -->
        <div class="relative z-10 h-full flex items-center justify-center px-4">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Main Headline -->
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 serif-heading leading-tight">
                    Con Đường Hạnh Phúc
                </h1>
                
                <!-- Sub-headline -->
                <p class="text-xl md:text-2xl text-gray-200 italic mb-12 serif-heading max-w-2xl mx-auto">
                    Chữa lành nỗi đau - Vun bồi nội lực - Khai mở trí tuệ
                </p>
                
                <!-- CTA Button -->
                @guest
                <div class="space-y-4">
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-4 px-8 rounded-full text-lg sans-serif-body transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-spa mr-3"></i>
                        Bắt đầu Hành trình
                    </a>
                    
                    <!-- Secondary Link -->
                    <div class="text-white/80 sans-serif-body">
                        <span>Đã có tài khoản?</span>
                        <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 underline ml-1 transition-colors">
                            Đăng nhập
                        </a>
                    </div>
                </div>
                @endguest
                
                @auth
                <div class="text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-white text-emerald-700 font-semibold py-3 px-8 rounded-full sans-serif-body transition-colors duration-200 hover:bg-gray-100">
                        <i class="fas fa-home mr-2"></i>
                        Vào Dashboard
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="fixed bottom-0 left-0 right-0 bg-black/50 backdrop-blur-sm text-white py-4 px-4 z-10">
        <div class="text-center text-sm sans-serif-body">
            <p>&copy; 2024 Con Đường Hạnh Phúc. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
</body>
</html>
