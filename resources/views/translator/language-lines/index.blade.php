@extends('layouts.translator')

@section('title', 'UI Translation Matrix')
@section('page-title', 'UI Translation Matrix')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Translation Matrix</h1>
            <p class="text-sm text-gray-600">Key | VI | EN | DE | KR</p>
            <p class="text-sm text-gray-600">Edit Vietnamese UI text based on English source</p>
        </div>
        <form method="GET" action="{{ route('translator.ui-matrix.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search group/key..."
                   class="w-72 rounded-xl bg-white border border-gray-300 text-gray-900 placeholder-gray-400 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Search</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Group/Key</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">English (Source)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vietnamese (Target)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($lines as $line)
                        @php
                            $text = (array) ($line->text ?? []);
                        @endphp
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $line->group }}.{{ $line->key }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $text['en'] ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <textarea rows="2" data-vi-input="{{ $line->id }}" class="w-80 rounded-xl bg-white border border-gray-300 text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $text['vi'] ?? '' }}</textarea>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" data-save-btn="{{ $line->id }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $lines->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-save-btn]');
    if (!btn) return;

    const id = btn.getAttribute('data-save-btn');
    const textarea = document.querySelector(`[data-vi-input="${id}"]`);
    if (!textarea) return;

    const vi = textarea.value;

    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Saving...';

    try {
        const res = await fetch(`{{ url('/translator/ui-matrix') }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ vi }),
        });

        if (!res.ok) {
            throw new Error('Save failed');
        }

        btn.textContent = 'Saved';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 900);
    } catch (err) {
        btn.textContent = 'Error';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 1200);
    } finally {
        btn.disabled = false;
    }
});
</script>
@endpush
