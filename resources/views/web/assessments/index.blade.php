@extends('layouts.app')

@section('title', 'Assessments')
@section('page-title', 'Assessments')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Assessments</h1>
        <p class="text-gray-600">Discover yourself through our comprehensive assessments</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Column 1: Available Assessments -->
        <div class="glass-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-emerald-600 mr-2"></i>
                    Available Assessments
                </h2>
                <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $allAvailableAssessments->count() }} Available
                </span>
            </div>

            @if($allAvailableAssessments->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No assessments available</h3>
                    <p class="text-gray-500 text-sm">Check back later for new assessments</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($allAvailableAssessments as $assessment)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-300 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">{{ $assessment->title }}</h3>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $assessment->description }}</p>
                                </div>
                                @if($assessment->status === 'special')
                                    <span class="ml-3 px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-star mr-1"></i>Special
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ $assessment->questions_count }} questions
                                </div>
                                
                                {{-- Check if user has already completed this assessment --}}
                                @php
                                    $hasCompleted = $userResults->where('assessment_id', $assessment->id)->isNotEmpty();
                                @endphp
                                
                                <a href="{{ route('user.assessments.show', $assessment) }}" 
                                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    @if($hasCompleted)
                                        <i class="fas fa-redo mr-1"></i>Retake
                                    @else
                                        <i class="fas fa-play mr-1"></i>Start
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Column 2: My Results (History) -->
        <div class="glass-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    My Results
                </h2>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $userResults->count() }} Completed
                </span>
            </div>

            @if($userResults->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-chart-line text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No results yet</h3>
                    <p class="text-gray-500 text-sm">Complete an assessment to see your results here</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($userResults as $result)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">{{ $result->assessment->title }}</h3>
                                    <p class="text-sm text-gray-600">
                                        Completed {{ $result->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">{{ $result->total_score }}</div>
                                    <div class="text-xs text-gray-500">points</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm">
                                    @if($result->submission_mode === 'submitted_for_consultation')
                                        <span class="text-amber-600">
                                            <i class="fas fa-user-md mr-1"></i>Consultation
                                        </span>
                                    @else
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>Self Review
                                        </span>
                                    @endif
                                    
                                    @if($result->consultation_thread)
                                        <a href="{{ route('user.consultations.show', ['consultation_id' => $result->consultation_thread->id]) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-comments mr-1"></i>View Thread
                                        </a>
                                    @endif
                                </div>
                                
                                <a href="{{ route('user.assessments.result', $result) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Review Result
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Info Section -->
    <div class="mt-8 glass-card rounded-xl shadow-lg p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-info-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">About Assessments</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-1">Self Review Mode</h4>
                        <p>Save your results for personal reflection and track your progress over time.</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-1">Consultation Mode</h4>
                        <p>Submit your results to our consultants for personalized advice and guidance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
