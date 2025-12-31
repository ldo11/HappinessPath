@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Gửi yêu cầu tư vấn</h1>
        <p class="text-white/70 text-sm">Mô tả vấn đề của bạn để chuyên gia có thể hỗ trợ tốt nhất.</p>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultations.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-white/80 text-sm mb-2" for="title">Tiêu đề</label>
                <input id="title" name="title" value="{{ old('title') }}" required
                       class="w-full rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30" placeholder="Ví dụ: Mất ngủ kéo dài..." />
                @error('title')
                    <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-white/80 text-sm mb-2" for="pain_point_id">Pain point</label>
                <select id="pain_point_id" name="pain_point_id" required
                        class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <option value="" class="text-gray-900">-- Chọn pain point --</option>
                    @foreach($painPoints as $pp)
                        <option value="{{ $pp->id }}" class="text-gray-900" @selected((string) old('pain_point_id') === (string) $pp->id)>
                            {{ $pp->name }}
                        </option>
                    @endforeach
                </select>
                @error('pain_point_id')
                    <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-white/80 text-sm mb-2" for="assigned_consultant_id">Available consultants (tùy chọn)</label>
                <select id="assigned_consultant_id" name="assigned_consultant_id"
                        class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <option value="" class="text-gray-900">-- Không chọn --</option>
                    @foreach($availableConsultants as $consultant)
                        <option value="{{ $consultant->id }}" class="text-gray-900" @selected((string) old('assigned_consultant_id') === (string) $consultant->id)>
                            {{ $consultant->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_consultant_id')
                    <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-white/80 text-sm mb-2" for="content">Nội dung</label>
                <textarea id="content" name="content" rows="7" required
                          class="w-full rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30" placeholder="Bạn đang gặp điều gì? Bạn mong muốn nhận được lời khuyên như thế nào?">{{ old('content') }}</textarea>
                @error('content')
                    <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('consultations.index') }}" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white/80 hover:bg-white/10">Hủy</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Gửi</button>
            </div>
        </form>
    </div>
</div>
@endsection
