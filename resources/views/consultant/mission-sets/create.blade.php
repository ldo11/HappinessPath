@extends('layouts.app')

@section('title', 'Create Mission Set')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('consultant.mission-sets.index', ['locale' => app()->getLocale()]) }}" class="text-white/60 hover:text-white text-sm flex items-center gap-1 mb-2">
            <i class="fas fa-arrow-left"></i> Back to Mission Sets
        </a>
        <h2 class="text-2xl font-bold text-white">Create New Mission Set</h2>
    </div>

    <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-6">
        <form method="POST" action="{{ route('consultant.mission-sets.store', ['locale' => app()->getLocale()]) }}">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-1">Set Name</label>
                    <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 placeholder-white/30" required placeholder="e.g., 30 Days of Mindfulness">
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 placeholder-white/30" placeholder="Brief description of this program..."></textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/80 mb-1">Type</label>
                    <select name="type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 [&>option]:text-gray-900">
                        <option value="standard">Standard</option>
                        <option value="premium">Premium</option>
                        <option value="special">Special Event</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('consultant.mission-sets.index', ['locale' => app()->getLocale()]) }}" class="px-4 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10">Cancel</a>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-white font-medium px-6 py-2 rounded-lg transition">Create Mission Set</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
