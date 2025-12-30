@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $thread->title }}</h1>
            <div class="text-white/60 text-sm mt-1">
                Status: <span class="text-white/80">{{ strtoupper($thread->status) }}</span>
                <span class="ml-2">| User: {{ $thread->user->name ?? ('#'.$thread->user_id) }}</span>
                @if($thread->relatedPainPoint)
                    <span class="ml-2">| Pain point: {{ $thread->relatedPainPoint->title }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('consultant.dashboard') }}" class="px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-white hover:bg-white/15">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 mb-6">
        <div class="text-white/90 whitespace-pre-line">{{ $thread->content }}</div>
        <div class="text-white/50 text-xs mt-4">{{ $thread->created_at?->format('Y-m-d H:i') }}</div>
    </div>

    <div class="space-y-4 mb-8">
        @foreach($thread->replies as $reply)
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="text-white font-semibold">{{ $reply->user->name ?? ('User #'.$reply->user_id) }}</div>
                    <div class="text-white/50 text-xs">{{ $reply->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div class="text-white/80 mt-3 whitespace-pre-line">{{ $reply->content }}</div>
            </div>
        @endforeach

        @if($thread->replies->count() === 0)
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 text-white/70">
                No replies yet.
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <form method="POST" action="{{ route('consultant.threads.reply', $thread) }}" class="space-y-4">
            @csrf
            <label class="block text-white/80 text-sm" for="content">Reply (Advice)</label>
            <textarea id="content" name="content" rows="5" required
                      class="w-full rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30" placeholder="Write your advice...">{{ old('content') }}</textarea>
            @error('content')
                <div class="text-red-300 text-sm">{{ $message }}</div>
            @enderror
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Send Reply</button>
            </div>
        </form>
    </div>
</div>
@endsection
