@extends('layouts.translator')

@section('title', 'UI Translation Matrix')
@section('page-title', 'UI Translation Matrix')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Translation Matrix</h1>
            <p class="text-sm text-gray-600">Key | EN (Source) | VI | DE | KR</p>
            <p class="text-sm text-gray-600">
                @if(request('group'))
                    Showing translations for group: <strong>{{ request('group') }}</strong>
                @else
                    Showing all translation groups
                @endif
                - Edit translations based on English source text
            </p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('translator.ui-matrix.index') }}" class="flex items-center gap-2">
                <select name="group" class="rounded-xl bg-white border border-gray-300 text-gray-900 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search group/key..."
                       class="w-72 rounded-xl bg-white border border-gray-300 text-gray-900 placeholder-gray-400 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Search</button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Group/Key</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">English (Source)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vietnamese</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">German</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Korean</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
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
                                <textarea rows="2" data-vi-input="{{ $line->id }}" class="w-48 rounded-xl bg-white border border-gray-300 text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $text['vi'] ?? '' }}</textarea>
                            </td>
                            <td class="px-4 py-3">
                                <textarea rows="2" data-de-input="{{ $line->id }}" class="w-48 rounded-xl bg-white border border-gray-300 text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $text['de'] ?? '' }}</textarea>
                            </td>
                            <td class="px-4 py-3">
                                <textarea rows="2" data-kr-input="{{ $line->id }}" class="w-48 rounded-xl bg-white border border-gray-300 text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $text['kr'] ?? '' }}</textarea>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-col gap-1">
                                    <button type="button" data-save-vi-btn="{{ $line->id }}" class="px-3 py-1 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">Save VI</button>
                                    <button type="button" data-save-de-btn="{{ $line->id }}" class="px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm">Save DE</button>
                                    <button type="button" data-save-kr-btn="{{ $line->id }}" class="px-3 py-1 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-sm">Save KR</button>
                                </div>
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
    // Handle Vietnamese save
    const viBtn = e.target.closest('[data-save-vi-btn]');
    if (viBtn) {
        await saveTranslation(viBtn, 'vi', '[data-vi-input]');
        return;
    }

    // Handle German save
    const deBtn = e.target.closest('[data-save-de-btn]');
    if (deBtn) {
        await saveTranslation(deBtn, 'de', '[data-de-input]');
        return;
    }

    // Handle Korean save
    const krBtn = e.target.closest('[data-save-kr-btn]');
    if (krBtn) {
        await saveTranslation(krBtn, 'kr', '[data-kr-input]');
        return;
    }
});

async function saveTranslation(btn, locale, inputSelector) {
    const id = btn.getAttribute(`data-save-${locale}-btn`);
    const textarea = document.querySelector(`${inputSelector}="${id}"]`);
    if (!textarea) return;

    const text = textarea.value;

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
            body: JSON.stringify({ [locale]: text }),
        });

        if (!res.ok) {
            throw new Error('Save failed');
        }

        btn.textContent = 'Saved!';
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
}
</script>
@endpush
