@extends('layouts.translator')

@section('title', 'Translator Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Assessments Requiring Translation</h2>
        @if($assessments->count() > 0)
            <div class="space-y-3">
                @foreach($assessments as $assessment)
                    <div class="border rounded p-4">
                        <h3 class="font-semibold">{{ $assessment->title[app()->getLocale()] ?? $assessment->title['en'] }}</h3>
                        <p class="text-sm text-gray-600">Status: {{ $assessment->status }}</p>
                        <a href="{{ route('translator.assessments.translate', $assessment) }}" class="inline-block mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Translate
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No assessments requiring translation at this time.</p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Translation Statistics</h2>
        <p class="text-gray-600">Total Language Lines: {{ $languageLinesCount }}</p>
    </div>
</div>
@endsection
