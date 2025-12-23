@extends('layouts.app')

@section('title', 'Your Path Begins - Happiness Path')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Progress Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Step 3 of 3</span>
                <span class="text-sm text-gray-500">Your Results</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-500 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Results Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Tree Icon and Status -->
            <div class="mb-8">
                <div class="w-24 h-24 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                    <i class="fas {{ $treeIcon }} {{ $treeColor }} text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Tree is {{ ucfirst($treeStatus) }}!</h1>
                <p class="text-gray-600 text-lg">{{ $treeMessage }}</p>
            </div>

            <!-- Tree Health Visualization -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Tree Health</span>
                    <span class="text-sm font-bold {{ $userTree->health >= 80 ? 'text-green-600' : ($userTree->health >= 50 ? 'text-yellow-600' : 'text-orange-600') }}">
                        {{ round($userTree->health) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="health-bar h-full rounded-full transition-all duration-1000 ease-out
                        {{ $userTree->health >= 80 ? 'bg-gradient-to-r from-green-400 to-green-600' : 
                           ($userTree->health >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-orange-400 to-orange-600') }}"
                         style="width: {{ $userTree->health }}%"></div>
                </div>
            </div>

            <!-- Assessment Results -->
            @if($quizResult)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Assessment Results</h3>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center p-4 bg-heart-50 rounded-lg">
                            <i class="fas fa-heart text-heart-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Heart</p>
                            <p class="text-xl font-bold text-heart-700">{{ $quizResult->heart_score }}</p>
                        </div>
                        <div class="text-center p-4 bg-grit-50 rounded-lg">
                            <i class="fas fa-fire text-grit-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Grit</p>
                            <p class="text-xl font-bold text-grit-700">{{ $quizResult->grit_score }}</p>
                        </div>
                        <div class="text-center p-4 bg-wisdom-50 rounded-lg">
                            <i class="fas fa-brain text-wisdom-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Wisdom</p>
                            <p class="text-xl font-bold text-wisdom-700">{{ $quizResult->wisdom_score }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Your dominant focus area is <strong>{{ ucfirst($quizResult->dominant_issue) }}</strong>. 
                            We'll tailor your daily tasks to help you grow in this area.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Personalized Message -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">What's Next?</h3>
                    <div class="space-y-3 text-left">
                        <div class="flex items-start">
                            <i class="fas fa-sun text-yellow-500 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">Daily Tasks</p>
                                <p class="text-sm text-gray-600">Receive personalized daily activities to nurture your tree</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-spa text-green-500 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">Meditation</p>
                                <p class="text-sm text-gray-600">Guided sessions to improve mental clarity and peace</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-users text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900">Community</p>
                                <p class="text-sm text-gray-600">Connect with others on similar paths in {{ $user->city }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Journey Button -->
            <form method="POST" action="{{ route('onboarding.complete') }}">
                @csrf
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-play mr-2"></i>
                    Begin My Journey
                </button>
            </form>

            <p class="text-sm text-gray-500 mt-4">
                Your personalized path to happiness awaits! 🌱
            </p>
        </div>

        <!-- Encouragement Quote -->
        <div class="mt-8 text-center">
            <blockquote class="text-lg text-gray-600 italic">
                "The journey of a thousand miles begins with a single step."
            </blockquote>
            <p class="text-sm text-gray-500 mt-2">- Lao Tzu</p>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Animate the health bar on page load
document.addEventListener('DOMContentLoaded', function() {
    const healthBar = document.querySelector('.health-bar');
    const targetWidth = '{{ $userTree->health }}%';
    
    // Start from 0 and animate to target
    healthBar.style.width = '0%';
    setTimeout(() => {
        healthBar.style.width = targetWidth;
    }, 500);
    
    // Add celebration effect if health is good
    const health = {{ $userTree->health }};
    if (health >= 80) {
        confetti();
    }
});

// Simple confetti effect for good results
function confetti() {
    const colors = ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'];
    const container = document.body;
    
    for (let i = 0; i < 50; i++) {
        const confettiPiece = document.createElement('div');
        confettiPiece.style.position = 'fixed';
        confettiPiece.style.width = '10px';
        confettiPiece.style.height = '10px';
        confettiPiece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confettiPiece.style.left = Math.random() * 100 + '%';
        confettiPiece.style.top = '-10px';
        confettiPiece.style.borderRadius = '50%';
        confettiPiece.style.zIndex = '9999';
        confettiPiece.style.pointerEvents = 'none';
        
        container.appendChild(confettiPiece);
        
        // Animate falling
        const duration = Math.random() * 3 + 2;
        const horizontalMovement = (Math.random() - 0.5) * 100;
        
        confettiPiece.animate([
            { transform: 'translateY(0) translateX(0) rotate(0deg)', opacity: 1 },
            { transform: `translateY(100vh) translateX(${horizontalMovement}px) rotate(${Math.random() * 720}deg)`, opacity: 0 }
        ], {
            duration: duration * 1000,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        }).onfinish = () => confettiPiece.remove();
    }
}
</script>
@endsection
@endsection
