@extends('layouts.admin')

@section('title', 'Pain Points')
@section('page-title', 'Pain Points')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">Pain Points</h2>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($painPoints as $p)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $p->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $p->category }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $p->icon }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t">
        {{ $painPoints->links() }}
    </div>
</div>
@endsection
