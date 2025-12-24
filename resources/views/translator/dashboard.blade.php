@extends('layouts.translator')

@section('title', 'Translator Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="{{ route('translator.app-translations.index') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 rounded-full">
                <i class="fas fa-file-export text-indigo-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Manage application strings</p>
                <p class="text-xl font-bold text-gray-800">App Translations</p>
            </div>
        </div>
    </a>

    <a href="{{ route('translator.users.index') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Create & manage accounts</p>
                <p class="text-xl font-bold text-gray-800">Users</p>
            </div>
        </div>
    </a>
</div>
@endsection
