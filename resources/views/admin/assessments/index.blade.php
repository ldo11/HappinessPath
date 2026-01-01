@extends('layouts.admin')

@section('title', 'Manage Assessments')
@section('page-title', 'Manage Assessments')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Advanced Assessments</h2>
        <p class="text-sm text-gray-600 mt-1">Manage assessment workflows and translations</p>
    </div>
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.assessments.import-json') }}" enctype="multipart/form-data" class="flex items-center gap-2">
            @csrf
            <input type="file" name="json_file" accept="application/json" class="text-sm" required>
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-upload mr-2"></i>Upload JSON
            </button>
        </form>
        <a href="{{ route('admin.assessments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Create Assessment
        </a>
    </div>
</div>

<!-- Assessments Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($assessments as $assessment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $assessment->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($assessment->description, 80) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $assessment->creator->name }}</div>
                                <div class="text-sm text-gray-500">{{ $assessment->creator->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $assessment->questions_count }} questions</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($assessment->status)
                            @case('created')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-edit mr-1"></i>Created
                                </span>
                                @break
                            @case('translated')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-language mr-1"></i>Translated
                                </span>
                                @break
                            @case('reviewed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-check-circle mr-1"></i>Reviewed
                                </span>
                                @break
                            @case('active')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Active
                                </span>
                                @break
                            @case('special')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-star mr-1"></i>Special
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $assessment->created_at->format('M j, Y') }}
                        <div class="text-xs text-gray-400">{{ $assessment->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <!-- Edit Action -->
                            <a href="{{ route('admin.assessments.edit', $assessment) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Status Actions -->
                            @if($assessment->status === 'created')
                                <form method="POST" action="{{ route('admin.assessments.request-translation', $assessment) }}" class="inline"
                                      onsubmit="return confirm('Send translation request to translators?')">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900" title="Request Translation">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            @endif

                            @if($assessment->status === 'translated')
                                <form method="POST" action="{{ route('admin.assessments.mark-reviewed', $assessment) }}" class="inline"
                                      onsubmit="return confirm('Mark this assessment as reviewed?')">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Mark as Reviewed">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                            @endif

                            @if($assessment->status === 'reviewed')
                                <form method="POST" action="{{ route('admin.assessments.approve-publish', $assessment) }}" class="inline"
                                      onsubmit="return confirm('Publish this assessment for all users?')">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Approve & Publish">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.assessments.mark-special', $assessment) }}" class="inline"
                                      onsubmit="return confirm('Mark this assessment as special (hidden, consultant assigned only)?')">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-900" title="Mark as Special">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif

                            <!-- Delete Action -->
                            <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($assessments->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No assessments found</h3>
        <p class="text-gray-600 mb-4">Get started by creating your first assessment.</p>
        <a href="{{ route('admin.assessments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Create Assessment
        </a>
    </div>
@endif

<!-- Status Legend -->
<div class="mt-6 bg-white rounded-lg shadow p-4">
    <h4 class="text-sm font-medium text-gray-900 mb-3">Status Workflow:</h4>
    <div class="flex flex-wrap gap-4 text-xs">
        <div class="flex items-center">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">Created</span>
            <span class="text-gray-600">→</span>
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mx-2">Translated</span>
            <span class="text-gray-600">→</span>
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mx-2">Reviewed</span>
            <span class="text-gray-600">→</span>
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mx-2">Active</span>
        </div>
        <div class="flex items-center">
            <span class="text-gray-600">or</span>
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 mx-2">Special</span>
            <span class="text-gray-600">(consultant only)</span>
        </div>
    </div>
</div>
@endsection
