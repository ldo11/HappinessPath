@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Tư vấn</h1>
            <p class="text-white/70 text-sm">Danh sách yêu cầu tư vấn của bạn.</p>
        </div>
        <a href="{{ route('consultations.create') }}" class="px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-white hover:bg-white/15">Gửi yêu cầu tư vấn</a>
    </div>

    <div class="space-y-4">
        @forelse($threads as $thread)
            <a href="{{ route('consultations.show', $thread) }}" class="block rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-5 hover:bg-white/15 transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-white font-semibold">{{ $thread->title }}</div>
                        <div class="text-white/70 text-sm mt-1 line-clamp-2">{{ $thread->content }}</div>
                        <div class="text-white/50 text-xs mt-3">{{ $thread->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3 py-1 rounded-full text-xs border border-white/15 text-white/80">
                            {{ strtoupper($thread->status) }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 text-white/80">
                Bạn chưa có yêu cầu tư vấn nào.
            </div>
        @endforelse

        <div>
            {{ $threads->links() }}
        </div>
    </div>
</div>
@endsection
