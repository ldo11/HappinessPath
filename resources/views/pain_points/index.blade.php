@extends('layouts.app')

@section('title', 'Nỗi khổ')

@section('content')
<div class="min-h-screen">
    <div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 spiritual-font">Quản lý tất cả vấn đề</h1>
                <p class="text-gray-600 text-sm">Chọn/bỏ chọn và điều chỉnh mức độ (0-10).</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-gray-100 text-gray-900 px-4 py-2.5 rounded-lg text-center shadow-sm hover:shadow-md transition">
                Quay lại Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.pain-points.store') }}" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($painPoints as $painPoint)
                    @php
                        $selectedScore = (int) ($userPainPoints[$painPoint->id] ?? 0);
                        $isActive = $selectedScore > 0;
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <!-- Hidden ID input ensures the ID is always sent -->
                            <input type="hidden" name="pain_points[{{ $painPoint->id }}][id]" value="{{ $painPoint->id }}">
                            
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $painPoint->getTranslatedName() }}</div>
                                <div class="mt-4">
                                    <input type="range" min="0" max="10" step="1" 
                                        name="pain_points[{{ $painPoint->id }}][score]" 
                                        value="{{ $selectedScore }}" 
                                        class="w-full accent-emerald-500" 
                                        oninput="document.getElementById('pp_level_full_{{ $painPoint->id }}').textContent = this.value; if(this.value > 0) { this.closest('.bg-white').classList.add('ring-2', 'ring-emerald-500'); } else { this.closest('.bg-white').classList.remove('ring-2', 'ring-emerald-500'); }">
                                    <div class="mt-2 text-sm text-gray-600">Mức độ: <span id="pp_level_full_{{ $painPoint->id }}">{{ $selectedScore }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <button type="submit" class="bg-gray-900 text-white px-5 py-3 rounded-lg">Lưu / Cập nhật</button>
            </div>
        </form>

        <hr class="my-10 border-gray-200">

        <!-- Request New Pain Point Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 max-w-2xl">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Đề xuất nỗi khổ mới</h2>
            <p class="text-gray-600 mb-6">Bạn có vấn đề nào chưa có trong danh sách? Hãy đề xuất thêm.</p>
            
            <form method="POST" action="{{ route('user.pain-points.request') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Tên vấn đề</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Mô tả (tùy chọn)</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                    Gửi đề xuất
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
