@extends('layouts.app')

@section('title', 'Dashboard - Your Happiness Path')

@section('content')
<div class="min-h-screen pb-20">
    <!-- Tree Status Header -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-primary-100">{{ $treeStatus['message'] }}</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-xl font-bold">{{ $userTree->exp }}</span>
                    </div>
                    <p class="text-sm text-primary-200">Level {{ $userTree->season }}</p>
                </div>
            </div>

            <!-- Tree Health Bar -->
            <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <i class="fas {{ $treeStatus['icon'] }} {{ $treeStatus['color'] }}"></i>
                        <span class="font-medium">{{ $treeStatus['level'] }}</span>
                    </div>
                    <span class="text-sm">{{ round($userTree->health) }}% Health</span>
                </div>
                <div class="w-full bg-white/30 rounded-full h-3 overflow-hidden">
                    <div class="health-bar h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500"
                         style="width: {{ $userTree->health }}%"></div>
                </div>
                <p class="text-xs text-primary-100 mt-1">
                    {{ $treeStatus['next_level'] }}% to next level
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-6 space-y-6">
        <!-- Today's Task -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Day {{ $userJourney->current_day }} Task
                        </h2>
                        <p class="text-blue-100 text-sm">Your daily moment of growth</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $todayTask->estimated_minutes ?? 5 }} min
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $todayTask->title }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $todayTask->description }}</p>
                </div>

                @if($todayTask->solution_id)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-video mr-2"></i>
                            This task includes a guided video. Find a comfortable space and follow along.
                        </p>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-{{ $todayTask->type === 'mindfulness' ? 'brain' : ($todayTask->type === 'physical' ? 'running' : 'heart') }} mr-1"></i>
                            {{ ucfirst($todayTask->type) }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-signal mr-1"></i>
                            {{ ucfirst($todayTask->difficulty) }}
                        </span>
                    </div>
                    <button onclick="completeTask()" 
                            id="completeTaskBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Complete Task
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('meditate') }}" 
               class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-200">
                <i class="fas fa-spa text-3xl mb-3 animate-pulse-slow"></i>
                <h3 class="font-bold text-lg mb-1">Meditate</h3>
                <p class="text-purple-100 text-sm">Find inner peace</p>
            </a>

            <button onclick="openDonateModal()" 
                    class="bg-gradient-to-r from-heart-500 to-heart-600 text-white rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-200">
                <i class="fas fa-heart text-3xl mb-3 animate-bounce-slow"></i>
                <h3 class="font-bold text-lg mb-1">Give Fruit</h3>
                <p class="text-heart-100 text-sm">{{ $userTree->fruits_balance }} available</p>
            </button>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history mr-2 text-gray-600"></i>
                Recent Activity
            </h3>
            
            @if($recentDonations->count() > 0)
                <div class="space-y-3">
                    @foreach($recentDonations as $donation)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-heart-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-heart text-heart-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Donated to {{ $donation->receiver->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $donation->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-green-600 font-medium">+5 EXP</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-seedling text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-600">No recent activity</p>
                    <p class="text-sm text-gray-500">Complete tasks and donate fruits to see your activity here</p>
                </div>
            @endif
        </div>

        <!-- Community Connection -->
        @if($nearbyUsers->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-gray-600"></i>
                    Nearby Community Members
                </h3>
                
                <div class="grid grid-cols-1 gap-3">
                    @foreach($nearbyUsers->take(3) as $nearbyUser)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-primary-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $nearbyUser->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $nearbyUser->city }}</p>
                                </div>
                            </div>
                            <button onclick="donateToUser({{ $nearbyUser->id }})" 
                                    class="text-heart-600 hover:text-heart-700 text-sm font-medium">
                                <i class="fas fa-heart mr-1"></i>Give
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Donate Fruit Modal -->
<div id="donateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Donate Fruit</h3>
            <button onclick="closeDonateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="mb-6">
            <p class="text-gray-600 mb-4">Share your positive energy with a community member. You have <span class="font-bold text-green-600">{{ $userTree->fruits_balance }}</span> fruits available.</p>
            
            <div class="space-y-2 max-h-60 overflow-y-auto" id="userList">
                @foreach($nearbyUsers as $nearbyUser)
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="receiver_id" value="{{ $nearbyUser->id }}" class="mr-3">
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-primary-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $nearbyUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $nearbyUser->city }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Message (optional)</label>
            <textarea id="donateMessage" rows="3" 
                      placeholder="Send a message of encouragement..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
        </div>

        <div class="flex space-x-3">
            <button onclick="closeDonateModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="submitDonation()" 
                    class="flex-1 px-4 py-2 bg-heart-600 hover:bg-heart-700 text-white rounded-lg">
                <i class="fas fa-heart mr-2"></i>Donate Fruit
            </button>
        </div>
    </div>
</div>

@section('scripts')
<script>
function completeTask() {
    const btn = document.getElementById('completeTaskBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Completing...';

    fetch('{{ route("dashboard.complete.task") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Update UI
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Something went wrong', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Complete Task';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Something went wrong', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Complete Task';
    });
}

function openDonateModal() {
    document.getElementById('donateModal').classList.remove('hidden');
}

function closeDonateModal() {
    document.getElementById('donateModal').classList.add('hidden');
}

function donateToUser(userId) {
    // Select the user in the modal
    const radio = document.querySelector(`input[name="receiver_id"][value="${userId}"]`);
    if (radio) {
        radio.checked = true;
        openDonateModal();
    }
}

function submitDonation() {
    const selectedUser = document.querySelector('input[name="receiver_id"]:checked');
    const message = document.getElementById('donateMessage').value;

    if (!selectedUser) {
        showNotification('Please select a user to donate to', 'error');
        return;
    }

    const submitBtn = event.target;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Donating...';

    fetch('{{ route("dashboard.donate.fruit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            receiver_id: selectedUser.value,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeDonateModal();
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Something went wrong', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Something went wrong', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-heart mr-2"></i>Donate Fruit';
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('donateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDonateModal();
    }
});
</script>
@endsection
@endsection
