@extends('layouts.app')

@section('title', 'Edit Assessment')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Edit Assessment</h2>
            <p class="text-sm text-white/60 mt-1">Update assessment details, title, description, and status.</p>
        </div>
        <a href="{{ route('consultant.assessments.index', ['locale' => app()->getLocale()]) }}" class="text-white/80 hover:text-white">Back</a>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl p-6">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('consultant.assessments.update', ['locale' => app()->getLocale(), 'assessment' => $assessment]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Title *</label>
                    <p class="text-xs text-white/50 mb-2">Provide title in at least one language (Vietnamese or English)</p>
                    <div class="grid grid-cols-1 gap-3">
                        <input type="text" name="title[vi]" value="{{ old('title.vi', $assessment->getTranslation('title', 'vi')) }}" placeholder="Vietnamese title"
                               class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                        <input type="text" name="title[en]" value="{{ old('title.en', $assessment->getTranslation('title', 'en')) }}" placeholder="English title"
                               class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                    </div>
                    @error('title.vi')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('title.en')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Description *</label>
                    <p class="text-xs text-white/50 mb-2">Provide description in at least one language (Vietnamese or English)</p>
                    <div class="grid grid-cols-1 gap-3">
                        <textarea name="description[vi]" rows="3" placeholder="Vietnamese description"
                                  class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('description.vi', $assessment->getTranslation('description', 'vi')) }}</textarea>
                        <textarea name="description[en]" rows="3" placeholder="English description"
                                  class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('description.en', $assessment->getTranslation('description', 'en')) }}</textarea>
                    </div>
                    @error('description.vi')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('description.en')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">Status</label>
                    <p class="text-xs text-white/50 mb-2">Change assessment publication status</p>
                    <select name="status" class="w-full rounded-xl bg-white/10 border border-white/15 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-white/30">
                        <option class="text-gray-900" value="created" @selected(old('status', $assessment->status) === 'created')">📝 Draft (Not visible to users)</option>
                        <option class="text-gray-900" value="active" @selected(old('status', $assessment->status) === 'active')">✅ Published (Visible to users)</option>
                    </select>
                    @error('status')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                <h3 class="text-white font-semibold mb-2">📊 Assessment Information</h3>
                <div class="text-white/70 text-sm space-y-1">
                    <p><strong>📝 Questions:</strong> {{ $assessment->questions->count() }}</p>
                    <p><strong>📊 Current Status:</strong> 
                        @if($assessment->status === 'active')
                            <span class="text-emerald-300">✅ Published</span>
                        @else
                            <span class="text-yellow-300">📝 Draft</span>
                        @endif
                    </p>
                    <p><strong>📅 Created:</strong> {{ $assessment->created_at->format('Y-m-d H:i') }}</p>
                    @if($assessment->creator)
                        <p><strong>👤 Created by:</strong> {{ $assessment->creator->email }}</p>
                    @endif
                </div>
                <div class="mt-3 p-3 bg-white/5 rounded-lg">
                    <p class="text-white/60 text-sm">
                        <strong>ℹ️ Note:</strong> You can edit the title, description, and status of any assessment. 
                        To edit questions and options, please contact an administrator.
                    </p>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-white/60 text-sm">
                    * Required fields - provide content in at least one language
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl bg-white text-gray-900 hover:bg-gray-100 font-medium">
                    💾 Update Assessment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
