@extends('layouts.app')

@section('title', 'Videos')

@section('content')
<div class="min-h-screen">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 spiritual-font">Video Learning</h1>
            <p class="text-gray-600 text-sm">Video hiển thị theo ngôn ngữ và tôn giáo đã chọn trong Profile.</p>
        </div>
        <div class="flex flex-col gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mr-1">Pillar</span>
                <a href="{{ route('videos.index', array_filter(['source' => $source])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $pillar === null ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">All</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => 'body', 'source' => $source])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $pillar === 'body' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Thân</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => 'mind', 'source' => $source])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $pillar === 'mind' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Tâm</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => 'wisdom', 'source' => $source])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $pillar === 'wisdom' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Trí</a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mr-1">Source</span>
                <a href="{{ route('videos.index', array_filter(['pillar' => $pillar])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $source === null ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">All</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => $pillar, 'source' => 'buddhism'])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $source === 'buddhism' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Phật</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => $pillar, 'source' => 'christianity'])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $source === 'christianity' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Chúa</a>
                <a href="{{ route('videos.index', array_filter(['pillar' => $pillar, 'source' => 'science'])) }}" class="px-4 py-2 rounded-lg border text-sm {{ $source === 'science' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-900 border-gray-200' }}">Khoa học</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($videos as $video)
            <a href="{{ route('videos.show', ['videoId' => $video->id]) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 overflow-hidden">
                @if($video->thumbnail_url)
                    <div class="aspect-video bg-gray-100">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-play text-gray-400 text-2xl"></i>
                    </div>
                @endif
                <div class="p-5">
                    <div class="font-semibold text-gray-900">{{ $video->title }}</div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @php
                            $pillarTags = is_array($video->pillar_tags) ? $video->pillar_tags : [];
                            $sourceTags = is_array($video->source_tags) ? $video->source_tags : [];
                            $pillarMap = ['body' => 'Thân', 'mind' => 'Tâm', 'wisdom' => 'Trí'];
                            $sourceMap = ['buddhism' => 'Phật', 'christianity' => 'Chúa', 'science' => 'Khoa học'];
                        @endphp
                        @foreach($pillarTags as $t)
                            @if(isset($pillarMap[$t]))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $pillarMap[$t] }}</span>
                            @endif
                        @endforeach
                        @foreach($sourceTags as $t)
                            @if(isset($sourceMap[$t]))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $sourceMap[$t] }}</span>
                            @endif
                        @endforeach
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">+{{ (int) $video->xp_reward }} XP</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 md:col-span-2 lg:col-span-3 xl:col-span-4">
                <p class="text-gray-600">Chưa có video nào cho chủ đề này.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $videos->links() }}
    </div>
</div>
@endsection
