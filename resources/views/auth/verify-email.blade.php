@extends('layouts.guest')

@section('title', 'Verify Email')

@section('auth-subtitle', 'Xác thực email để tiếp tục hành trình')

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white spiritual-font">Xác thực email</h1>
        <p class="mt-3 text-sm text-white/80">Chúng tôi đã gửi cho bạn một liên kết xác thực. Vui lòng kiểm tra hộp thư.</p>
    </div>

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full emerald-gradient text-white px-4 py-3 rounded-lg hover:shadow-lg transition-all duration-200">
                Resend Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-sm text-white/70 hover:text-white underline transition-colors">
                Log Out
            </button>
        </form>
    </div>
@endsection
