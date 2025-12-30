@extends('layouts.admin')

@section('title', 'Videos')
@section('page-title', 'Videos')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Video List</h3>
        <a href="{{ route('admin.videos.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Add Video
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pillar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($videos as $video)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $video->title }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xl">{{ $video->url }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($video->embed_url)
                                <button type="button"
                                        class="group relative w-28 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-gray-300"
                                        onclick="openVideoPreview('{{ $video->embed_url }}', @json($video->title))">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full bg-gray-100"></div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/25 group-hover:bg-black/35 flex items-center justify-center">
                                        <div class="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center">
                                            <i class="fas fa-play text-gray-900 text-xs"></i>
                                        </div>
                                    </div>
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $video->language ?? 'vi' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ implode(', ', (array) ($video->pillar_tags ?? [])) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ implode(', ', (array) ($video->source_tags ?? [])) }}</td>
                        <td class="px-6 py-4">
                            @if($video->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Yes</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.videos.edit', $video) }}" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                            <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700" onclick="return confirm('Delete this video?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $videos->links() }}
        </div>
    </div>
</div>

<div id="videoPreviewModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/50" onclick="closeVideoPreview()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <div class="font-semibold text-gray-900" id="videoPreviewTitle">Preview</div>
                <button type="button" class="text-gray-500 hover:text-gray-900" onclick="closeVideoPreview()">
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

@section('scripts')
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
@endsection
