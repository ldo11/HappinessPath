@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Total Users</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-language text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Active Languages</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_languages'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-video text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Total Solutions</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_solutions'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Pending Reviews</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_translations'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'volunteer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Solutions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Recent Solutions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentSolutions as $solution)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-video text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $solution->type }}</p>
                            <p class="text-xs text-gray-500">{{ $solution->translations->count() }} translations</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        {{ strtoupper($solution->locale) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
