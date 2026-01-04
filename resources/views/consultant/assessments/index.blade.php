@extends('layouts.app')

@section('title', 'Assessments')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Assessments</h2>
            <p class="text-sm text-white/60 mt-1">Create and manage assessments for users.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('consultant.assessments.import-json', ['locale' => app()->getLocale()]) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="json_file" accept="application/json" class="text-sm text-white/80" required>
                <button type="submit" class="px-4 py-2 rounded-lg border border-white/15 text-white/80 hover:text-white hover:bg-white/5">Upload JSON</button>
            </form>
            <a href="{{ route('consultant.assessments.create', ['locale' => app()->getLocale()]) }}" class="bg-white text-gray-900 hover:bg-gray-100 px-4 py-2 rounded-lg">Create</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Assessment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($assessments as $assessment)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ is_array($assessment->title) ? ($assessment->title['en'] ?? $assessment->title['vi'] ?? '') : $assessment->title }}</div>
                                <div class="text-sm text-white/60 mt-1">{{ is_array($assessment->description) ? ($assessment->description['en'] ?? $assessment->description['vi'] ?? '') : $assessment->description }}</div>
                            </td>
                            <td class="px-6 py-4 text-white/80">{{ $assessment->status }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('consultant.assessments.show', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="text-white/80 hover:text-white">View</a>
                                <a href="{{ route('consultant.assessments.edit', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="text-emerald-300 hover:text-emerald-200">Edit</a>
                                @if(auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('consultant.assessments.destroy', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="inline" onsubmit="return confirm('Delete this assessment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 text-red-300 hover:text-red-200">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
