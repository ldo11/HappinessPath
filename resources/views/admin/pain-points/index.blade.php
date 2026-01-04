@extends('layouts.admin')

@section('title', 'Pain Points Management')
@section('page-title', 'Pain Points Management')

@section('content')
<div class="space-y-8">
    <!-- Pending Requests -->
    @if($pendingPainPoints->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden border border-yellow-200">
        <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-yellow-800">Pending Requests</h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ $pendingPainPoints->count() }} new
            </span>
        </div>
        <ul class="divide-y divide-gray-200">
            @foreach($pendingPainPoints as $painPoint)
            <li class="p-6">
                <form action="{{ route('user.admin.pain-points.approve', $painPoint->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name (Default/Current Locale)</label>
                                    <input type="text" name="name" value="{{ $painPoint->getTranslatedName() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="mind" @selected($painPoint->category == 'mind')>Mind</option>
                                        <option value="body" @selected($painPoint->category == 'body')>Body</option>
                                        <option value="wisdom" @selected($painPoint->category == 'wisdom')>Wisdom</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-gray-500">Description: </span>
                                <span class="text-sm text-gray-900">{{ $painPoint->getTranslatedDescription() }}</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-400">
                                Requested by: {{ $painPoint->createdByUser->name ?? 'Unknown' }} on {{ $painPoint->created_at->format('Y-m-d') }}
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Approve
                            </button>
                            <button type="submit" form="reject-form-{{ $painPoint->id }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reject
                            </button>
                        </div>
                    </div>
                </form>
                <form id="reject-form-{{ $painPoint->id }}" action="{{ route('user.admin.pain-points.reject', $painPoint->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Active Pain Points -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Active Pain Points</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name ({{ app()->getLocale() }})</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users Affected</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($activePainPoints as $p)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $p->getTranslatedName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($p->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $p->users()->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $activePainPoints->links() }}
        </div>
    </div>
    
    <!-- Rejected Pain Points (Collapsible or bottom) -->
    @if($rejectedPainPoints->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden opacity-75">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
             <h3 class="text-lg font-medium text-gray-700">Rejected Requests</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
             <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rejectedPainPoints as $p)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->getTranslatedName() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->category }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $rejectedPainPoints->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
