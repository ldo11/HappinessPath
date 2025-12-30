@extends('layouts.admin')

@section('title', 'Create Assessment Question')
@section('page-title', 'Create Assessment Question')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">Create Assessment Question</h2>

<form method="POST" action="{{ route('admin.assessment-questions.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
    @csrf

    <div>
        <label class="block text-sm text-gray-700 mb-1">Pillar</label>
        <select name="pillar_group" class="w-full border rounded-lg px-3 py-2">
            <option value="heart">heart</option>
            <option value="grit">grit</option>
            <option value="wisdom">wisdom</option>
        </select>
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Order</label>
        <input type="number" name="order" value="1" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Content (VI)</label>
        <textarea name="content_vi" class="w-full border rounded-lg px-3 py-2" rows="3"></textarea>
    </div>

    <div>
        <label class="block text-sm text-gray-700 mb-1">Content (EN)</label>
        <textarea name="content_en" class="w-full border rounded-lg px-3 py-2" rows="3"></textarea>
    </div>

    <div class="pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Save</button>
        <a href="{{ route('admin.assessment-questions.index') }}" class="ml-3 text-gray-700">Cancel</a>
    </div>
</form>
@endsection
