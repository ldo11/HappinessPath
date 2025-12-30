@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $thread->title }}</h1>
            <div class="text-white/60 text-sm mt-1">
                Status: <span class="text-white/80">{{ strtoupper($thread->status) }}</span>
                <span class="ml-2">| User: {{ $thread->user->name ?? ('#'.$thread->user_id) }}</span>
                @if($thread->relatedPainPoint)
                    <span class="ml-2">| Pain point: {{ $thread->relatedPainPoint->title }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('consultant.dashboard') }}" class="px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-white hover:bg-white/15">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 mb-6">
        <div class="text-white/90 whitespace-pre-line">{{ $thread->content }}</div>
        <div class="text-white/50 text-xs mt-4">{{ $thread->created_at?->format('Y-m-d H:i') }}</div>
    </div>

    <div class="space-y-4 mb-8">
        <!-- System Messages -->
        @if(isset($thread->systemMessages) && $thread->systemMessages->count() > 0)
            @foreach($thread->systemMessages as $systemMessage)
                <div class="rounded-2xl bg-blue-500/20 border border-blue-500/30 backdrop-blur-xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="{{ $systemMessage->icon }} text-blue-400 mr-2"></i>
                            <div class="text-blue-300 font-semibold text-sm">{{ $systemMessage->type_label }}</div>
                        </div>
                        <div class="text-blue-400/60 text-xs">{{ $systemMessage->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="text-blue-200 mt-3">{{ $systemMessage->content }}</div>
                </div>
            @endforeach
        @endif

        <!-- User Replies -->
        @foreach($thread->replies as $reply)
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="text-white font-semibold">{{ $reply->user->name ?? ('User #'.$reply->user_id) }}</div>
                    <div class="text-white/50 text-xs">{{ $reply->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div class="text-white/80 mt-3 whitespace-pre-line">{{ $reply->content }}</div>
            </div>
        @endforeach

        @if($thread->replies->count() === 0 && (!isset($thread->systemMessages) || $thread->systemMessages->count() === 0))
            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6 text-white/70">
                No replies yet.
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        <!-- Assessment Assignment Tool -->
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-emerald-300 font-semibold">
                    <i class="fas fa-clipboard-list mr-2"></i>Assign Assessment
                </h3>
                <button type="button" onclick="toggleAssessmentAssignment()" 
                        class="text-emerald-400 hover:text-emerald-300 text-sm">
                    <i class="fas fa-chevron-down" id="assessmentToggleIcon"></i>
                </button>
            </div>
            
            <div id="assessmentAssignmentForm" class="hidden space-y-3">
                <form method="POST" action="{{ route('consultant.threads.assign-assessment', $thread) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-emerald-200 text-sm mb-1">Select Assessment:</label>
                        <select name="assessment_id" id="assessmentSelect" required
                                class="w-full rounded-lg bg-white/10 border border-white/20 text-emerald-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400/50">
                            <option value="">Loading assessments...</option>
                        </select>
                    </div>
                    <button type="submit" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-1"></i>Assign Assessment
                    </button>
                </form>
            </div>
        </div>

        <!-- Reply Form -->
        <form method="POST" action="{{ route('consultant.threads.reply', $thread) }}" class="space-y-4">
            @csrf
            <label class="block text-white/80 text-sm" for="content">Reply (Advice)</label>
            <textarea id="content" name="content" rows="5" required
                      class="w-full rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30" placeholder="Write your advice...">{{ old('content') }}</textarea>
            @error('content')
                <div class="text-red-300 text-sm">{{ $message }}</div>
            @enderror
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Send Reply</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let assessmentsLoaded = false;

function toggleAssessmentAssignment() {
    const form = document.getElementById('assessmentAssignmentForm');
    const icon = document.getElementById('assessmentToggleIcon');
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        
        // Load assessments if not already loaded
        if (!assessmentsLoaded) {
            loadAssessments();
        }
    } else {
        form.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

async function loadAssessments() {
    const select = document.getElementById('assessmentSelect');
    
    try {
        const response = await fetch('{{ route("consultant.assessments.available") }}');
        const assessments = await response.json();
        
        // Clear loading option
        select.innerHTML = '<option value="">Select an assessment...</option>';
        
        // Add assessment options
        assessments.forEach(assessment => {
            const option = document.createElement('option');
            option.value = assessment.id;
            option.textContent = `${assessment.title} (${assessment.status})`;
            select.appendChild(option);
        });
        
        assessmentsLoaded = true;
    } catch (error) {
        console.error('Failed to load assessments:', error);
        select.innerHTML = '<option value="">Failed to load assessments</option>';
    }
}

// Auto-load assessments when page loads if form is expanded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we should auto-expand the assignment form (optional)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('assign') === 'true') {
        toggleAssessmentAssignment();
    }
});
</script>
@endpush
@endsection
