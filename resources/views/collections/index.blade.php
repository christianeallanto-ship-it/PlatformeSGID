@extends('layouts.app')

@section('title', 'Gestion des Collectes - BENINCLEAN')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="truck" class="text-purple-600"></i>
            Collectes
        </h1>
        <p class="text-sm text-slate-500">Planifiez les tournées, assignez les chauffeurs et suivez le statut d'avancement de la collecte.</p>
    </div>

    <!-- Feedback messages -->
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Panel: List of Collections -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Filter Bar -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <form method="GET" action="{{ route('collections.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Search field -->
                    <div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Chauffeur ou itinéraire..." 
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 px-3 py-2">
                    </div>

                    <!-- Status filter -->
                    <div>
                        <select name="status" id="status" class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2">
                            <option value="">Tous les statuts</option>
                            <option value="Planifiée" {{ request('status') === 'Planifiée' ? 'selected' : '' }}>Planifiée</option>
                            <option value="En cours" {{ request('status') === 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="Terminée" {{ request('status') === 'Terminée' ? 'selected' : '' }}>Terminée</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            Filtrer
                        </button>
                        @if(request()->filled('search') || request()->filled('status'))
                            <a href="{{ route('collections.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold py-2 px-3 rounded-lg flex items-center justify-center transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Itinéraire / Zone</th>
                                <th class="px-6 py-4">Chauffeur</th>
                                <th class="px-6 py-4">Heure prévue</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-right">Changer statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            @forelse($collections as $collection)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 block">{{ $collection->route_name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-xs font-semibold text-slate-700">
                                                {{ strtoupper(substr($collection->driver_name, 0, 1)) }}
                                            </div>
                                            <span>{{ $collection->driver_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $collection->scheduled_at->format('d/m/Y H:i') }}
                                        <span class="text-[10px] block text-slate-400">({{ $collection->scheduled_at->diffForHumans() }})</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusStyle = 'bg-blue-50 text-blue-700 border-blue-200';
                                            if ($collection->status === 'En cours') {
                                                $statusStyle = 'bg-orange-50 text-orange-700 border-orange-200';
                                            } elseif ($collection->status === 'Terminée') {
                                                $statusStyle = 'bg-green-50 text-green-700 border-green-200';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-0.5 border text-xs font-semibold rounded-full {{ $statusStyle }}">
                                            {{ $collection->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('collections.updateStatus', $collection) }}" class="inline-flex items-center gap-1.5 justify-end">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" 
                                                    class="text-xs border-slate-200 rounded-lg py-1 px-2.5 focus:ring-green-500 focus:border-green-500 bg-slate-50">
                                                <option value="Planifiée" {{ $collection->status === 'Planifiée' ? 'selected' : '' }}>Planifiée</option>
                                                <option value="En cours" {{ $collection->status === 'En cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="Terminée" {{ $collection->status === 'Terminée' ? 'selected' : '' }}>Terminée</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="truck" class="w-8 h-8 text-slate-300"></i>
                                            <span>Aucune collecte de planifiée en ce moment.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($collections->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">
                            Affichage de <span class="font-semibold text-slate-700">{{ $collections->firstItem() }}</span>
                            à <span class="font-semibold text-slate-700">{{ $collections->lastItem() }}</span>
                            sur <span class="font-semibold text-slate-700">{{ $collections->total() }}</span> tournée(s)
                        </p>
                        {{ $collections->links() }}
                    </div>
                @else
                    <div class="px-6 py-3 border-t border-slate-100">
                        <p class="text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">{{ $collections->total() }}</span> tournée(s) au total
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Panel: Create a new collection -->
        <div class="lg:col-span-4">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 sticky top-6">
                <h2 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="text-green-600"></i>
                    Planifier une tournée
                </h2>

                <form method="POST" action="{{ route('collections.store') }}" class="space-y-4">
                    @csrf

                    <!-- Route field -->
                    <div>
                        <label for="route_name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Itinéraire / Zone</label>
                        <select name="route_name" id="route_name" required 
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2">
                            <option value="">Sélectionner une zone</option>
                            <option value="Akpakpa - Sainte Rita">Akpakpa - Sainte Rita</option>
                            <option value="Zongo - Houénoussou">Zongo - Houénoussou</option>
                            <option value="Gbedjromede - Fidjrossè">Gbedjromede - Fidjrossè</option>
                            <option value="Cadjehoun - Gbégamey">Cadjehoun - Gbégamey</option>
                            <option value="Kouhounou - Saint Michel">Kouhounou - Saint Michel</option>
                            <option value="Menontin - Agla">Menontin - Agla</option>
                        </select>
                        @error('route_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Driver name field -->
                    <div>
                        <label for="driver_name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Conducteur (Chauffeur)</label>
                        <input type="text" name="driver_name" id="driver_name" required 
                               placeholder="Nom complet du chauffeur..." 
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 px-3 py-2">
                        @error('driver_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scheduled date field -->
                    <div>
                        <label for="scheduled_at" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Date et heure programmées</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required 
                               class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 px-3 py-2">
                        @error('scheduled_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status field -->
                    <div>
                        <label for="status_new" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut initial</label>
                        <select name="status" id="status_new" required 
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2">
                            <option value="Planifiée" selected>Planifiée</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminée">Terminée</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 text-sm mt-6">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Créer la tournée
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
