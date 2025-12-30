@extends('layouts.app')

@section('title', $video->title)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 -mt-8">
    <div class="bg-slate-950/60 border-y border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="{{ route('videos.index') }}" class="text-sm text-white/70 hover:text-white">&larr; Quay lại Thư viện</a>
            <div class="mt-3 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white spiritual-font">{{ $video->title }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-white/10 text-white/80 border border-white/10">{{ $video->language ?? 'vi' }}</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">+{{ (int) $video->xp_reward }} XP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-2xl overflow-hidden border border-white/10 bg-black shadow-2xl">
            <div class="aspect-video">
                <iframe class="w-full h-full" src="{{ $video->embed_url }}" title="{{ $video->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            @php
                $alreadyClaimed = (bool) ($log && $log->claimed_at);
            @endphp

            <div>
                <button id="claimBtn"
                        type="button"
                        data-claimed="{{ $alreadyClaimed ? '1' : '0' }}"
                        class="emerald-gradient text-white px-6 py-3 rounded-xl hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                        @disabled($alreadyClaimed)>
                    {{ $alreadyClaimed ? 'Đã nhận XP' : 'Mark as Watched (+XP)' }}
                </button>
                <div class="text-sm text-white/70 mt-2" id="claimHint">
                    @if($alreadyClaimed)
                        Bạn đã nhận XP cho video này.
                    @else
                        Khi xem xong, bấm nút để nhận XP.
                    @endif
                </div>
            </div>

            <div class="text-sm text-white/60">
                XP chỉ được nhận 1 lần cho mỗi video.
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-lg font-semibold text-white">Related Videos</h2>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse(($relatedVideos ?? collect()) as $rv)
                    <a href="{{ route('videos.show', ['locale' => app()->getLocale(), 'videoId' => $rv->id]) }}" class="block rounded-2xl bg-white/10 border border-white/10 hover:bg-white/15 transition overflow-hidden">
                        @if($rv->thumbnail_url)
                            <div class="aspect-video bg-black/60">
                                <img src="{{ $rv->thumbnail_url }}" alt="" class="w-full h-full object-cover" />
                            </div>
                        @endif
                        <div class="p-4">
                            <div class="text-white font-semibold text-sm line-clamp-2">{{ $rv->title }}</div>
                            <div class="mt-2 text-xs text-white/70">+{{ (int) $rv->xp_reward }} XP</div>
                        </div>
                    </a>
                @empty
                    <div class="text-white/60">No related videos.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
(function () {
    const btn = document.getElementById('claimBtn');
    if (!btn) return;

    function toast(message, type) {
        const el = document.createElement('div');
        el.className = `fixed top-20 right-4 z-50 glassmorphism rounded-lg px-5 py-3 text-white max-w-sm ${type === 'success' ? '' : 'text-red-200'}`;
        el.innerHTML = `<div class="flex items-center gap-2"><i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-circle text-red-400'}"></i><span>${message}</span></div>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    btn.addEventListener('click', async function () {
        if (btn.getAttribute('data-claimed') === '1') {
            return;
        }

        btn.disabled = true;

        try {
            const res = await fetch(@json(route('videos.claim', ['locale' => app()->getLocale(), 'videoId' => $video->id])), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });

            const data = await res.json();

            if (!res.ok) {
                toast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                btn.disabled = false;
                return;
            }

            if (data.claimed) {
                btn.setAttribute('data-claimed', '1');
                btn.textContent = 'Đã nhận XP';
                toast(`Bạn nhận được +${data.xp_awarded} XP!`, 'success');
                const hint = document.getElementById('claimHint');
                if (hint) hint.textContent = 'Bạn đã nhận XP cho video này.';
                return;
            }

            toast('Bạn đã nhận XP cho video này rồi.', 'error');
            btn.setAttribute('data-claimed', '1');
            btn.textContent = 'Đã nhận XP';
        } catch (e) {
            toast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            btn.disabled = false;
        }
    });
})();
</script>
@endsection
@endsection
