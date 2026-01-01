@extends('layouts.consultant')

@section('title', 'Videos')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Videos</h2>
        <a href="{{ route('consultant.videos.create', ['locale' => app()->getLocale()]) }}" class="bg-white text-gray-900 hover:bg-gray-100 px-4 py-2 rounded-lg">+ Add Video</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Preview</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Lang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Pillar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Active</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($videos as $video)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $video->title }}</div>
                                <div class="text-sm text-white/60 truncate max-w-xl">{{ $video->url }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($video->embed_url)
                                    <button type="button"
                                            class="group relative w-28 h-16 rounded-lg overflow-hidden border border-white/10 hover:border-white/20"
                                            onclick="openVideoPreview('{{ $video->embed_url }}', @json($video->title))">
                                        @if($video->thumbnail_url)
                                            <img src="{{ $video->thumbnail_url }}" alt="" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full bg-white/10"></div>
                                        @endif
                                        <div class="absolute inset-0 bg-black/25 group-hover:bg-black/35 flex items-center justify-center">
                                            <div class="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center">
                                                <i class="fas fa-play text-gray-900 text-xs"></i>
                                            </div>
                                        </div>
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-white/80">{{ $video->language ?? 'vi' }}</td>
                            <td class="px-6 py-4 text-sm text-white/80">{{ implode(', ', (array) ($video->pillar_tags ?? [])) }}</td>
                            <td class="px-6 py-4 text-sm text-white/80">{{ implode(', ', (array) ($video->source_tags ?? [])) }}</td>
                            <td class="px-6 py-4">
                                @if($video->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-100 border border-emerald-400/20">Yes</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/10 text-white/70 border border-white/10">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('consultant.videos.edit', ['locale' => app()->getLocale(), 'videoId' => $video]) }}" class="text-emerald-300 hover:text-emerald-200">Edit</a>
                                <form method="POST" action="{{ route('consultant.videos.destroy', ['locale' => app()->getLocale(), 'videoId' => $video]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-300 hover:text-red-200 ml-2" onclick="return confirm('Delete this video?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-white/10">
            {{ $videos->links() }}
        </div>
    </div>
</div>

<div id="videoPreviewModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60" onclick="closeVideoPreview()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="glassmorphism rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden border border-white/10">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                <div class="font-semibold text-white" id="videoPreviewTitle">Preview</div>
                <button type="button" class="text-white/70 hover:text-white" onclick="closeVideoPreview()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="aspect-video w-full bg-black rounded-xl overflow-hidden">
                    <iframe id="videoPreviewIframe" class="w-full h-full" src="" title="Video preview" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openVideoPreview(embedUrl, title) {
        const modal = document.getElementById('videoPreviewModal');
        const iframe = document.getElementById('videoPreviewIframe');
        const titleEl = document.getElementById('videoPreviewTitle');

        titleEl.textContent = title || 'Preview';
        iframe.src = embedUrl;
        modal.classList.remove('hidden');
    }

    function closeVideoPreview() {
        const modal = document.getElementById('videoPreviewModal');
        const iframe = document.getElementById('videoPreviewIframe');
        iframe.src = '';
        modal.classList.add('hidden');
    }
</script>
@endpush
