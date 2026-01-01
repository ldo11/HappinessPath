@extends('layouts.translator')

@section('title', 'Daily Mission Translations')
@section('page-title', 'Daily Mission Translations')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Daily Mission Translations</h2>
    <p class="text-sm text-gray-600 mt-1">Translate daily missions (auto-detect source language)</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mission</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pillars</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($missions as $mission)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @php
                            $tEn = trim((string) $mission->getTranslation('title', 'en'));
                            $tVi = trim((string) $mission->getTranslation('title', 'vi'));
                            $displayTitle = $tEn !== '' ? $tEn : $tVi;
                        @endphp
                        <div class="text-sm font-medium text-gray-900">{{ $displayTitle }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($mission->getTranslation('description', 'en') ?: $mission->getTranslation('description', 'vi'), 90) }}</div>
                        <div class="mt-2 flex gap-2">
                            @if($tEn === '')
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs">Needs EN</span>
                            @endif
                            @if($tVi === '')
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs">Needs VI</span>
                            @endif
                            @if($tEn !== '' && $tVi !== '')
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs">OK</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($mission->is_body)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 mr-1">Body</span>
                        @endif
                        @if($mission->is_mind)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 mr-1">Mind</span>
                        @endif
                        @if($mission->is_wisdom)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Wisdom</span>
                        @endif
                        @if(!$mission->is_body && !$mission->is_mind && !$mission->is_wisdom)
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $mission->points }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $mission->createdBy?->name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('translator.daily-missions.show', $mission) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-sm">
                            <i class="fas fa-pen-to-square mr-1"></i>Edit Translation
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $missions->links() }}
    </div>
</div>
@endsection
