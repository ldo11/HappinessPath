@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Users & Journey Progress</h2>
        <p class="text-sm text-gray-600 mt-1">Track user progress through their wellness journey</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-2"></i>Add User
    </a>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" placeholder="Search by name or email..." 
                   value="{{ request('search') }}" 
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="role" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="translator" {{ request('role') == 'translator' ? 'selected' : '' }}>Translator</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
            Clear
        </a>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tree Stats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($user->email_verified_at)
                                        <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Verified</span>
                                    @else
                                        <span class="text-xs text-yellow-600"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $user->city ?? 'No location' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                               ($user->role === 'translator' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->userJourney)
                            <div class="space-y-1">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-900 font-medium">Day {{ $user->userJourney->current_day }}/30</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ number_format($user->userJourney->progress_percentage, 1) }}%)</span>
                                </div>
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $user->userJourney->progress_percentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-600">
                                    Focus: {{ $user->userJourney->focus_label }}
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">No journey data</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->userTree)
                            <div class="space-y-1 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    <span class="text-gray-900">{{ $user->userTree->exp }} EXP</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-heart text-red-500 mr-1"></i>
                                    <span class="text-gray-900">{{ $user->userTree->fruits_balance }} fruits</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-seedling text-green-500 mr-1"></i>
                                    <span class="text-gray-900">{{ $user->userTree->season_label }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">No tree data</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->userJourney)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->userJourney->journey_status === 'Đang hoạt động' ? 'bg-green-100 text-green-800' :
                                   ($user->userJourney->journey_status === 'Tạm dừng' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-red-100 text-red-800') }}">
                                {{ $user->userJourney->journey_status }}
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Not Started
                            </span>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">
                            Joined {{ $user->created_at->format('M j, Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @if($user->role === 'user')
                            <form method="POST" action="{{ route('admin.users.reset-assessment', $user) }}" class="inline mr-3"
                                  onsubmit="return confirm('Reset assessment for this user? They will be required to take it again on next login.')">
                                @csrf
                                <button type="submit" class="text-amber-600 hover:text-amber-900">
                                    <i class="fas fa-undo"></i> Reset Assessment
                                </button>
                            </form>
                        @endif
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $users->links() }}
    </div>
</div>

<!-- Summary Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-2xl font-bold text-gray-900">{{ $users->total() }}</div>
                <div class="text-sm text-gray-600">Total Users</div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-play text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-2xl font-bold text-gray-900">{{ $users->filter(fn($u) => $u->userJourney && $u->userJourney->current_day > 1)->count() }}</div>
                <div class="text-sm text-gray-600">Active Journeys</div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-hands-helping text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-2xl font-bold text-gray-900">{{ $users->filter(fn($u) => $u->role === 'translator')->count() }}</div>
                <div class="text-sm text-gray-600">Translators</div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-tree text-purple-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-2xl font-bold text-gray-900">{{ $users->filter(fn($u) => $u->userTree)->sum('userTree.exp') }}</div>
                <div class="text-sm text-gray-600">Total EXP</div>
            </div>
        </div>
    </div>
</div>
@endsection
