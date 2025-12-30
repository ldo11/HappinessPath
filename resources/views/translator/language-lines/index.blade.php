@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Translation Matrix</h1>
            <p class="text-sm text-white/70">Key | VI | EN | DE | KR</p>
        </div>
        <form method="GET" action="{{ route('translator.language-lines.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search group/key..."
                   class="w-72 rounded-xl bg-white/10 border border-white/15 text-white placeholder-white/40 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-white/30" />
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Search</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">Key</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">VI</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">EN</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">DE</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">KR</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($lines as $line)
                        @php
                            $text = (array) ($line->text ?? []);
                        @endphp
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-white">{{ $line->group }}.{{ $line->key }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('translator.language-lines.update', $line) }}" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="search" value="{{ $search }}" />
                                    <textarea name="vi" rows="2" class="w-64 rounded-xl bg-white/10 border border-white/15 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('vi', $text['vi'] ?? '') }}</textarea>
                            </td>
                            <td class="px-4 py-3">
                                    <textarea name="en" rows="2" class="w-64 rounded-xl bg-white/10 border border-white/15 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('en', $text['en'] ?? '') }}</textarea>
                            </td>
                            <td class="px-4 py-3">
                                    <textarea name="de" rows="2" class="w-64 rounded-xl bg-white/10 border border-white/15 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('de', $text['de'] ?? '') }}</textarea>
                            </td>
                            <td class="px-4 py-3">
                                    <textarea name="kr" rows="2" class="w-64 rounded-xl bg-white/10 border border-white/15 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('kr', $text['kr'] ?? '') }}</textarea>
                            </td>
                            <td class="px-4 py-3 text-right">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-white text-gray-900 hover:bg-gray-100">Save</button>
                                </form>
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
