@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.mission-sets.index') }}" class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
            &larr; Back to List
        </a>
        <h1 class="text-3xl font-bold">{{ $missionSet->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $missionSet->description }}</p>
        <div class="mt-4 flex gap-2">
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">{{ ucfirst($missionSet->type) }}</span>
            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">Created by {{ $missionSet->creator->name ?? 'Unknown' }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Missions List -->
        <div class="md:col-span-2">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Daily Missions ({{ $missionSet->missions->count() }}/30)</h2>
                
                @if($missionSet->missions->count() > 0)
                    <div class="space-y-4">
                        @foreach($missionSet->missions as $mission)
                            <div class="border rounded p-4 flex justify-between items-center hover:bg-gray-50">
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase">Day {{ $mission->day_number }}</span>
                                    <h3 class="font-medium text-lg">{{ $mission->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($mission->description, 100) }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-emerald-600">+{{ $mission->points }} XP</div>
                                    <div class="flex gap-1 mt-1 justify-end">
                                        @if($mission->is_body)<i class="fas fa-running text-blue-500" title="Body"></i>@endif
                                        @if($mission->is_mind)<i class="fas fa-brain text-pink-500" title="Mind"></i>@endif
                                        @if($mission->is_wisdom)<i class="fas fa-compass text-yellow-500" title="Wisdom"></i>@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded border border-dashed">
                        No missions added yet.
                    </div>
                @endif
            </div>

            <!-- Add Mission Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Add Mission</h3>
                <form action="{{ route('admin.mission-sets.missions.store', $missionSet) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Day Number</label>
                            <input type="number" name="day_number" min="1" max="30" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">XP Points</label>
                            <input type="number" name="points" value="10" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Title (English)</label>
                        <input type="text" name="title[en]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description (English)</label>
                        <textarea name="description[en]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-700 mb-2">Focus Areas</span>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_body" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <span class="ml-2">Body</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_mind" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <span class="ml-2">Mind</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_wisdom" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <span class="ml-2">Wisdom</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Add Mission</button>
                </form>
            </div>
        </div>

        <!-- Assignment Sidebar -->
        <div class="md:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Assign to User</h2>
                <form action="{{ route('admin.mission-sets.assign', $missionSet) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">User ID</label>
                        <input type="number" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Enter User ID" required>
                        <p class="text-xs text-gray-500 mt-1">Enter the ID of the user to assign this program to.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Assign Program
                    </button>
                </form>
            </div>
            
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="font-bold mb-3">Active Users</h3>
                @if($missionSet->activeUsers->count() > 0)
                    <ul class="space-y-2">
                        @foreach($missionSet->activeUsers as $user)
                            <li class="text-sm flex justify-between">
                                <span>{{ $user->name }}</span>
                                <span class="text-gray-500 text-xs">Day {{ $user->mission_started_at ? $user->mission_started_at->diffInDays(now()) + 1 : '?' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No active users.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
