@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $threadModel->title }}</h1>
            <div class="text-white/60 text-sm mt-1">
                Trạng thái: <span class="text-white/80">{{ strtoupper($threadModel->status) }}</span>
                @if($threadModel->relatedPainPoint)
                    <span class="ml-2">| Pain point: {{ $threadModel->relatedPainPoint->name }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('consultations.index') }}" class="px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-white hover:bg-white/15">Quay lại</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 mb-6">
        <div class="text-white/90 whitespace-pre-line">{{ $threadModel->content }}</div>
        <div class="text-white/50 text-xs mt-4">{{ $threadModel->created_at?->format('Y-m-d H:i') }}</div>
    </div>

    <div class="space-y-4 mb-8">
        @foreach($threadModel->replies as $reply)
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="text-white font-semibold">
                        {{ $reply->user_id === auth()->id() ? 'Bạn' : ($reply->user->name ?? 'Consultant') }}
                    </div>
                    <div class="text-white/50 text-xs">{{ $reply->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div class="text-white/80 mt-3 whitespace-pre-line">{{ $reply->content }}</div>
            </div>
        @endforeach

        @if($threadModel->replies->count() === 0)
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 text-white/70">
                Chưa có phản hồi nào.
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultations.reply', $threadModel) }}" class="space-y-4">
            @csrf
            <label class="block text-white/80 text-sm" for="content">Gửi phản hồi</label>
            <textarea id="content" name="content" rows="4" required
                      class="w-full rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30" placeholder="Nhập phản hồi của bạn...">{{ old('content') }}</textarea>
            @error('content')
                <div class="text-red-300 text-sm">{{ $message }}</div>
            @enderror
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Gửi</button>
            </div>
        </form>
    </div>
</div>
@endsection
