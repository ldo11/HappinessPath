@extends('layouts.app')

@section('title', 'Assessment Result')
@section('page-title', 'Assessment Result')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Result Header -->
    <div class="glass-card rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $userAssessment->assessment->title }}</h1>
                <p class="text-gray-600">Completed on {{ $userAssessment->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-blue-600">{{ $results['total_score'] }}</div>
                <div class="text-sm text-gray-500">Total Score</div>
                <div class="text-lg text-gray-600 mt-1">{{ round($results['percentage'], 1) }}%</div>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            @if($userAssessment->submission_mode === 'submitted_for_consultation')
                <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-user-md mr-1"></i>Submitted for Consultation
                </span>
                @if($userAssessment->consultation_thread)
                    <a href="{{ route('consultations.show', $userAssessment->consultation_thread) }}" 
                       class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-blue-200 transition-colors">
                        <i class="fas fa-comments mr-1"></i>View Consultation Thread
                    </a>
                @endif
            @else
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Self Review
                </span>
            @endif
        </div>
    </div>

    <!-- Overall Performance -->
    <div class="glass-card rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Overall Performance
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $results['total_score'] }}</div>
                <div class="text-sm text-gray-600">Score Achieved</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-600">{{ $results['max_score'] }}</div>
                <div class="text-sm text-gray-600">Maximum Score</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-emerald-600">{{ round($results['percentage'], 1) }}%</div>
                <div class="text-sm text-gray-600">Performance</div>
            </div>
        </div>

        <!-- Performance Bar -->
        <div class="mt-6">
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-blue-500 to-emerald-500 h-4 rounded-full transition-all duration-500" 
                     style="width: {{ min($results['percentage'], 100) }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>0%</span>
                <span>50%</span>
                <span>100%</span>
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    <div class="glass-card rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-list-check text-emerald-600 mr-2"></i>
            Detailed Results
        </h2>

        <div class="space-y-6">
            @foreach($results['question_results'] as $index => $questionResult)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 mb-2">Question {{ $index + 1 }}</h3>
                            <p class="text-gray-700">{{ $questionResult['question']->content }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <div class="text-lg font-bold text-blue-600">{{ $questionResult['score_earned'] }}</div>
                            <div class="text-xs text-gray-500">/ {{ $questionResult['max_score'] }} points</div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-700">Your Answer:</div>
                                <div class="text-gray-800">{{ $questionResult['selected_option']->content }}</div>
                            </div>
                            @if($questionResult['score_earned'] === $questionResult['max_score'])
                                <span class="text-emerald-600">
                                    <i class="fas fa-star"></i>
                                    Best Answer
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Performance indicator -->
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ ($questionResult['score_earned'] / $questionResult['max_score']) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recommendations -->
    <div class="glass-card rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
            Recommendations
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($results['percentage'] >= 80)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-800 mb-2">
                        <i class="fas fa-trophy mr-1"></i>Excellent Performance!
                    </h3>
                    <p class="text-green-700 text-sm">
                        You've shown great understanding in this assessment. Consider exploring more advanced topics or helping others who might benefit from your insights.
                    </p>
                </div>
            @elseif($results['percentage'] >= 60)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-thumbs-up mr-1"></i>Good Progress
                    </h3>
                    <p class="text-blue-700 text-sm">
                        You're doing well! Focus on the areas where you didn't get maximum points to continue improving.
                    </p>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="font-semibold text-amber-800 mb-2">
                        <i class="fas fa-seedling mr-1"></i>Room for Growth
                    </h3>
                    <p class="text-amber-700 text-sm">
                        This is a great starting point! Consider reviewing the material and retaking the assessment to see your improvement.
                    </p>
                </div>
            @endif

            @if($userAssessment->submission_mode === 'self_review')
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h3 class="font-semibold text-purple-800 mb-2">
                        <i class="fas fa-user-md mr-1"></i>Need Professional Guidance?
                    </h3>
                    <p class="text-purple-700 text-sm mb-3">
                        If you'd like personalized advice based on your results, consider submitting this assessment for consultation.
                    </p>
                    <button onclick="submitForConsultation()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-1"></i>Submit for Consultation
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('assessments.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium text-center transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Assessments
        </a>
        
        <a href="{{ route('assessments.show', $userAssessment->assessment) }}" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium text-center transition-colors">
            <i class="fas fa-redo mr-2"></i>Retake Assessment
        </a>

        @if($userAssessment->submission_mode === 'submitted_for_consultation' && $userAssessment->consultation_thread)
            <a href="{{ route('consultations.show', $userAssessment->consultation_thread) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium text-center transition-colors">
                <i class="fas fa-comments mr-2"></i>View Consultation
            </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
function submitForConsultation() {
    if (confirm('Convert this self-review to a consultation request? A consultant will review your results and provide personalized advice.')) {
        // Create a form to submit the consultation request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("assessments.submit", $userAssessment->assessment) }}';
        
        // Add CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        // Add existing answers
        const answers = @json($userAssessment->answers);
        for (const [questionId, optionId] of Object.entries(answers)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'answers[' + questionId + ']';
            input.value = optionId;
            form.appendChild(input);
        }
        
        // Add submission mode
        const mode = document.createElement('input');
        mode.type = 'hidden';
        mode.name = 'submission_mode';
        mode.value = 'submitted_for_consultation';
        form.appendChild(mode);
        
        document.body.appendChild(form);
        form.submit();
    }
}
@endpush
