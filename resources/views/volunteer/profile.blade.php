@extends('layouts.volunteer')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@php
    $pendingCount = \App\Models\SolutionTranslation::where('is_auto_generated', true)
        ->whereNull('reviewed_at')
        ->count();
@endphp

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Volunteer Profile</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info -->
                <div class="lg:col-span-1">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-blue-600 text-3xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900">{{ Auth::user()->name }}</h4>
                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Role:</span>
                                <span class="font-medium">{{ ucfirst(Auth::user()->role) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">City:</span>
                                <span class="font-medium">{{ Auth::user()->city ?? 'Not set' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Spiritual Preference:</span>
                                <span class="font-medium">{{ Auth::user()->spiritual_preference ?? 'Not set' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Member Since:</span>
                                <span class="font-medium">{{ Auth::user()->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats & Achievements -->
                <div class="lg:col-span-2">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">My Impact</h4>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-yellow-50 p-4 rounded-lg text-center">
                            <i class="fas fa-star text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-2xl font-bold text-gray-900">0</p>
                            <p class="text-sm text-gray-600">Total EXP</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <i class="fas fa-check text-blue-600 text-2xl mb-2"></i>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ \App\Models\SolutionTranslation::where('reviewed_by', Auth::id())->count() }}
                            </p>
                            <p class="text-sm text-gray-600">Translations Reviewed</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <i class="fas fa-heart text-purple-600 text-2xl mb-2"></i>
                            <p class="text-2xl font-bold text-gray-900">0</p>
                            <p class="text-sm text-gray-600">Fruits Given</p>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900 mb-3">Recent Activity</h5>
                        <div class="space-y-2">
                            @php
                                $recentReviews = \App\Models\SolutionTranslation::where('reviewed_by', Auth::id())
                                    ->latest('reviewed_at')
                                    ->take(5)
                                    ->get();
                            @endphp
                            @if($recentReviews->count() > 0)
                                @foreach($recentReviews as $review)
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            <span>Reviewed {{ $review->language->name }} translation</span>
                                        </div>
                                        <span class="text-gray-500">{{ $review->reviewed_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-600 text-sm">No recent activity. Start reviewing translations to make an impact!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('volunteer.translations.index') }}" 
                       class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-language text-blue-600 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Review Translations</p>
                            <p class="text-sm text-gray-600">{{ $pendingCount }} pending reviews</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('profile.settings.edit') }}" 
                       class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                        <i class="fas fa-cog text-purple-600 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Account Settings</p>
                            <p class="text-sm text-gray-600">Update your profile information</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
