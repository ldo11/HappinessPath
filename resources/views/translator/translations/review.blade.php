@extends('layouts.translator')

@section('title', 'Review Translation')
@section('page-title', 'Review Translation')

@section('content')
<div class="mb-4">
    <a href="{{ route('translator.translations.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left mr-2"></i>Back to Translation Review
    </a>
</div>

<div class="bg-white rounded-lg shadow mb-6 p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-language text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $translation->title }}</h3>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span><i class="fas fa-globe"></i> {{ $translation->language->name }} ({{ strtoupper($translation->locale) }})</span>
                    <span><i class="fas fa-{{ $translation->solution->type === 'video' ? 'video' : 'file-alt' }}"></i> {{ ucfirst($translation->solution->type) }}</span>
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $translation->solution->pillar_tag === 'heart' ? 'bg-red-100 text-red-800' :
                           ($translation->solution->pillar_tag === 'grit' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($translation->solution->pillar_tag) }}
                    </span>
                    <span><i class="fas fa-robot text-indigo-500"></i> Auto-generated</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Created</p>
            <p class="text-sm font-medium">{{ $translation->created_at->format('M j, Y H:i') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">
                <i class="fas fa-file-alt mr-2"></i>Original (Vietnamese)
            </h3>
        </div>
        <div class="p-6">
            @if($originalTranslation)
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Title:</h4>
                    <p class="text-gray-800 bg-gray-50 p-3 rounded">{{ $originalTranslation->title }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Content:</h4>
                    <div class="text-gray-800 bg-gray-50 p-3 rounded whitespace-pre-wrap">{{ $originalTranslation->content }}</div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">No Vietnamese original found</p>
                    <p class="text-sm text-gray-500">Showing solution URL instead:</p>
                    <a href="{{ $translation->solution->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                        {{ $translation->solution->url }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b bg-indigo-50">
            <h3 class="text-lg font-semibold text-indigo-800">
                <i class="fas fa-edit mr-2"></i>Translation ({{ $translation->language->name }})
            </h3>
        </div>
        <form method="POST" action="{{ route('translator.translations.approve', $translation) }}" id="reviewForm">
            @csrf
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title:</label>
                    <input type="text" name="title" required
                           value="{{ old('title', $translation->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content:</label>
                    <textarea name="content" rows="8" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('content', $translation->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        Edit the translation if needed, then approve or reject
                    </div>
                    <div class="space-x-3">
                        <button type="button" onclick="showRejectModal()"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                            <i class="fas fa-times mr-2"></i>Reject
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                            <i class="fas fa-check mr-2"></i>Approve (+10 EXP)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <h4 class="font-semibold text-indigo-800 mb-2">
        <i class="fas fa-lightbulb mr-2"></i>Translation Tips
    </h4>
    <ul class="text-sm text-indigo-700 space-y-1">
        <li>• Maintain a compassionate, healing tone appropriate for mental health content</li>
        <li>• Ensure cultural nuances are properly translated</li>
        <li>• Check that technical terms are accurately translated</li>
        <li>• Preserve the meaning and emotional impact of the original content</li>
        <li>• You'll earn 10 EXP for approved translations, 5 EXP for rejections</li>
    </ul>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Translation</h3>
            <form method="POST" action="{{ route('translator.translations.reject', $translation) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection:</label>
                    <textarea name="reason" rows="4" required
                              placeholder="Please explain why this translation is being rejected..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                        <i class="fas fa-times mr-2"></i>Reject (+5 EXP)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

window.onclick = function(event) {
    const modal = document.getElementById('rejectModal');
    if (event.target == modal) {
        hideRejectModal();
    }
}
</script>
@endsection
@endsection
