@extends('layouts.admin')

@section('title', 'Create Solution')
@section('page-title', 'Create Solution')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.solutions.store') }}" id="solutionForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="article" {{ old('type') == 'article' ? 'selected' : '' }}>Article</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pillar Tag -->
                    <div>
                        <label for="pillar_tag" class="block text-sm font-medium text-gray-700">Pillar Tag</label>
                        <select name="pillar_tag" id="pillar_tag" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="heart" {{ old('pillar_tag') == 'heart' ? 'selected' : '' }}>Heart</option>
                            <option value="grit" {{ old('pillar_tag') == 'grit' ? 'selected' : '' }}>Grit</option>
                            <option value="wisdom" {{ old('pillar_tag') == 'wisdom' ? 'selected' : '' }}>Wisdom</option>
                        </select>
                        @error('pillar_tag')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                    <input type="url" name="url" id="url" required
                           value="{{ old('url') }}"
                           placeholder="https://example.com/video-or-article-url"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author Name -->
                <div>
                    <label for="author_name" class="block text-sm font-medium text-gray-700">Author Name</label>
                    <input type="text" name="author_name" id="author_name"
                           value="{{ old('author_name') }}"
                           placeholder="Author or creator name"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('author_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Primary Language -->
                <div>
                    <label for="locale" class="block text-sm font-medium text-gray-700">Primary Language</label>
                    <select name="locale" id="locale" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach($languages as $language)
                            <option value="{{ $language->code }}" {{ old('locale') == $language->code ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('locale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Translations Section -->
            <div class="border-t">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Translations</h3>
                    <p class="text-sm text-gray-600">Add content for each language. At least one translation is required.</p>
                </div>

                <!-- Language Tabs -->
                <div class="border-b">
                    <nav class="flex space-x-8 px-6" aria-label="Language tabs">
                        @foreach($languages as $index => $language)
                            <button type="button" 
                                    class="language-tab py-4 px-1 border-b-2 font-medium text-sm {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    data-language="{{ $language->code }}"
                                    onclick="switchLanguage('{{ $language->code }}')">
                                {{ $language->name }}
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100">{{ strtoupper($language->code) }}</span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Translation Content -->
                <div class="px-6 py-4">
                    @foreach($languages as $language)
                        <div class="translation-content" data-language="{{ $language->code }}" {{ $loop->first ? '' : 'style="display:none;" }}>
                            <div class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Title ({{ $language->name }})
                                        @if($language->code === old('locale'))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input type="text" 
                                            name="translations[{{ $language->code }}][locale]" 
                                            value="{{ $language->code }}" 
                                            type="hidden">
                                    <input type="text" 
                                            name="translations[{{ $language->code }}][title]" 
                                            value="{{ old("translations.{$language->code}.title") }}"
                                            placeholder="Enter title in {{ $language->name }}"
                                            {{ $language->code === old('locale') ? 'required' : '' }}
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    @error("translations.{$language->code}.title")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Content ({{ $language->name }})
                                        @if($language->code === old('locale'))
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <textarea name="translations[{{ $language->code }}][content]" 
                                              rows="6"
                                              placeholder="Enter content/description in {{ $language->name }}"
                                              {{ $language->code === old('locale') ? 'required' : '' }}
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old("translations.{$language->code}.content") }}</textarea>
                                    @error("translations.{$language->code}.content")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.solutions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700">
                    Create Solution
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
function switchLanguage(languageCode) {
    // Hide all translation content
    document.querySelectorAll('.translation-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.language-tab').forEach(tab => {
        tab.classList.remove('border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected translation content
    document.querySelector(`.translation-content[data-language="${languageCode}"]`).style.display = 'block';
    
    // Add active state to selected tab
    const activeTab = document.querySelector(`.language-tab[data-language="${languageCode}"]`);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
}

// Auto-switch primary language tab when locale changes
document.getElementById('locale').addEventListener('change', function(e) {
    switchLanguage(e.target.value);
});
</script>
@endsection
@endsection
