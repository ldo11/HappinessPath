@extends('layouts.app')

@section('title', 'Translate Pain Points')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Translate Pain Points</h1>
        <a href="{{ route('translator.dashboard') }}" class="text-blue-600 hover:text-blue-800">Back to Dashboard</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
             <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search ID or content..." class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                    Search
                </button>
            </form>
        </div>

        <ul class="divide-y divide-gray-200">
            @foreach($painPoints as $painPoint)
            <li class="p-4 hover:bg-gray-50">
                <form action="{{ route('user.translator.pain-points.update', $painPoint->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Source / Info -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-500">ID: {{ $painPoint->id }}</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $painPoint->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($painPoint->status) }}
                                </span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Category</label>
                                <div class="mt-1 text-sm text-gray-900">{{ ucfirst($painPoint->category) }}</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Current Name ({{ app()->getLocale() }})</label>
                                <div class="mt-1 text-sm text-gray-900 font-medium">{{ $painPoint->getTranslatedName() }}</div>
                            </div>

                            @if($painPoint->getTranslatedDescription())
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Current Description</label>
                                <div class="mt-1 text-sm text-gray-500 italic">{{ $painPoint->getTranslatedDescription() }}</div>
                            </div>
                            @endif
                        </div>

                        <!-- Translation Inputs -->
                        <div class="space-y-4">
                            @foreach(['en', 'vi', 'de', 'kr'] as $lang)
                                <div class="border rounded-md p-3 bg-gray-50">
                                    <div class="flex items-center gap-2 mb-2">
                                        <img src="https://flagcdn.com/24x18/{{ $lang === 'en' ? 'gb' : ($lang === 'vi' ? 'vn' : $lang) }}.png" alt="{{ $lang }}" class="h-4 w-6 object-cover rounded shadow-sm">
                                        <span class="text-sm font-bold text-gray-700 uppercase">{{ $lang }}</span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-500">Name</label>
                                            <input type="text" name="name[{{ $lang }}]" 
                                                value="{{ is_array($painPoint->name) ? ($painPoint->name[$lang] ?? '') : ($lang == 'vi' ? $painPoint->name : '') }}" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500">Description</label>
                                            <textarea name="description[{{ $lang }}]" rows="2" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">{{ is_array($painPoint->description) ? ($painPoint->description[$lang] ?? '') : ($lang == 'vi' ? $painPoint->description : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Translations
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </li>
            @endforeach
        </ul>
        
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $painPoints->links() }}
        </div>
    </div>
</div>
@endsection
