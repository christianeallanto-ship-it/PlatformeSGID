@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<section class="grid grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 flex items-center justify-center rounded-lg">
            <i data-lucide="layers"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total bacs</p>
            <h2 class="text-2xl font-bold">142</h2>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 flex items-center justify-center rounded-lg">
            <i data-lucide="check-circle-2"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Bacs normaux</p>
            <h2 class="text-2xl font-bold">78</h2>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-50 flex items-center justify-center rounded-lg">
            <i data-lucide="alert-triangle"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Presque pleins</p>
            <h2 class="text-2xl font-bold">38</h2>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4">
        <div class="w-12 h-12 bg-red-50 flex items-center justify-center rounded-lg">
            <i data-lucide="alert-circle"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Bacs pleins</p>
            <h2 class="text-2xl font-bold">26</h2>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 flex items-center justify-center rounded-lg">
            <i data-lucide="truck"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Collectes</p>
            <h2 class="text-2xl font-bold">12</h2>
        </div>
    </div>

</section>

@endsection