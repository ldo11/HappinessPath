@extends('layouts.volunteer')

@section('title', 'App Translations')
@section('page-title', 'App Translations')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">App Translations</h2>
        <p class="text-sm text-gray-600">Download, edit, and upload translation keys.</p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('volunteer.app-translations.download', ['locale' => $locale]) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-download mr-2"></i>Download JSON
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-4">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('volunteer.app-translations.index') }}" class="flex flex-col md:flex-row gap-3">
        <div>
            <select name="locale" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="vi" {{ $locale === 'vi' ? 'selected' : '' }}>Vietnamese (vi)</option>
                <option value="en" {{ $locale === 'en' ? 'selected' : '' }}>English (en)</option>
            </select>
        </div>

        <div class="flex-1">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search key or text..."
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>Search
        </button>

        <a href="{{ route('volunteer.app-translations.index', ['locale' => $locale]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
            Clear
        </a>
    </form>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Upload JSON</h3>
    <form method="POST" action="{{ route('volunteer.app-translations.upload') }}" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3 items-start md:items-end">
        @csrf
        <div>
            <label class="block text-sm text-gray-700 mb-1">Locale</label>
            <select name="locale" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="vi" {{ $locale === 'vi' ? 'selected' : '' }}>vi</option>
                <option value="en" {{ $locale === 'en' ? 'selected' : '' }}>en</option>
            </select>
        </div>

        <div class="flex-1 w-full">
            <label class="block text-sm text-gray-700 mb-1">JSON file</label>
            <input type="file" name="file" accept="application/json" class="w-full" />
            @error('file')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-upload mr-2"></i>Upload & Apply
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Text</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($translations as $key => $value)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm font-mono text-gray-800 whitespace-nowrap">{{ $key }}</td>
                    <td class="px-6 py-3 text-sm text-gray-700 break-words">{{ $value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(empty($translations))
    <div class="bg-white rounded-lg shadow p-8 text-center mt-6">
        <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No translations found.</p>
    </div>
@endif
@endsection
