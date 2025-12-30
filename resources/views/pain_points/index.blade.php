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

        <form method="POST" action="{{ route('pain-points.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($painPoints as $painPoint)
                    @php
                        $selectedSeverity = (int) ($userPainPoints[$painPoint->id] ?? 0);
                        $checked = $selectedSeverity > 0;
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded border-gray-300" name="pain_points[{{ $painPoint->id }}][id]" value="{{ $painPoint->id }}" @checked($checked)>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $painPoint->name }}</div>
                                <div class="mt-4">
                                    <input type="range" min="0" max="10" step="1" name="pain_points[{{ $painPoint->id }}][severity]" value="{{ $checked ? $selectedSeverity : 0 }}" class="w-full accent-emerald-500" oninput="document.getElementById('pp_level_full_{{ $painPoint->id }}').textContent = this.value;">
                                    <div class="mt-2 text-sm text-gray-600">Mức độ: <span id="pp_level_full_{{ $painPoint->id }}">{{ $checked ? $selectedSeverity : 0 }}</span></div>
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
    </div>
</div>
@endsection
