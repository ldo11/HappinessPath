@extends('layouts.volunteer')

@section('title', 'Volunteer Dashboard')
@section('page-title', 'Dashboard')

@php
    $pendingCount = $stats['pending_translations'];
@endphp

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Pending Reviews</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_translations'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Reviewed Today</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['reviewed_today'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-language text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Total Reviewed</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_reviewed'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-star text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Current EXP</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['current_exp'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pending Translations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Pending Translations</h3>
            <a href="{{ route('volunteer.translations.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="p-6">
            @if($pendingTranslations->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingTranslations as $translation)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-language text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($translation->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $translation->language->name }} • {{ $translation->solution->type }}</p>
                            </div>
                        </div>
                        <a href="{{ route('volunteer.translations.review', $translation) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Review
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                    <p class="text-gray-600">No pending translations!</p>
                    <p class="text-sm text-gray-500">All translations have been reviewed.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Your Recent Reviews</h3>
        </div>
        <div class="p-6">
            @if($recentReviews->count() > 0)
                <div class="space-y-4">
                    @foreach($recentReviews as $review)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($review->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $review->language->name }} • {{ $review->reviewed_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-green-600 font-medium">
                            <i class="fas fa-star"></i> +10 EXP
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-language text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No reviews yet</p>
                    <p class="text-sm text-gray-500">Start reviewing translations to earn EXP!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('volunteer.translations.index') }}" 
           class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
            <i class="fas fa-language text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-gray-900">Review Translations</p>
                <p class="text-sm text-gray-600">Help improve AI translations</p>
            </div>
        </a>
        
        <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
            <i class="fas fa-tree text-green-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-gray-900">My Giving Tree</p>
                <p class="text-sm text-gray-600">Level 1 • 0 fruits given</p>
            </div>
        </a>
        
        <a href="{{ route('volunteer.profile') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
            <i class="fas fa-user text-purple-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-gray-900">My Profile</p>
                <p class="text-sm text-gray-600">Update your information</p>
            </div>
        </a>
    </div>
</div>
@endsection
