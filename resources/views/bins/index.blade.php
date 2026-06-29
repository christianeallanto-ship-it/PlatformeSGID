@extends('layouts.app')

@section('title', 'Bacs - BENINCLEAN')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="trash-2" class="text-green-600"></i>
                Gestion des bacs à ordures
            </h1>
            <p class="text-sm text-slate-500">Visualisez, filtrez et gérez l'état de tous les bacs déployés à {{ $mapCity }}.</p>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('bins.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search field -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Recherche</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Code ou localisation..." 
                           class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 pl-8 pr-3 py-2">
                    <div class="absolute left-2.5 top-2.5 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <!-- Status filter -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                <select name="status" id="status" class="w-full text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2">
                    <option value="">Tous les statuts</option>
                    <option value="Normal" {{ request('status') === 'Normal' ? 'selected' : '' }}>Normal (&lt; 60%)</option>
                    <option value="Presque plein" {{ request('status') === 'Presque plein' ? 'selected' : '' }}>Presque plein (60-80%)</option>
                    <option value="Plein" {{ request('status') === 'Plein' ? 'selected' : '' }}>Plein (&gt; 80%)</option>
                </select>
            </div>



            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filtrer
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('bins.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold py-2 px-3 rounded-lg flex items-center justify-center transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Bins -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Localisation / Adresse</th>
                        <th class="px-6 py-4">Niveau de remplissage</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Dernière mise à jour</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($bins as $bin)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $bin->code }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                    <span>{{ $bin->location }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 w-64">
                                <div class="flex items-center gap-3">
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        @php
                                            $barColor = 'bg-green-500';
                                            if ($bin->fill_level >= 80) {
                                                $barColor = 'bg-red-500';
                                            } elseif ($bin->fill_level >= 60) {
                                                $barColor = 'bg-yellow-500';
                                            }
                                        @endphp
                                        <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $bin->fill_level }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-700 w-8">{{ $bin->fill_level }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeStyle = 'bg-green-50 text-green-700 border-green-200';
                                    if ($bin->fill_level >= 80) {
                                        $badgeStyle = 'bg-red-50 text-red-700 border-red-200';
                                    } elseif ($bin->fill_level >= 60) {
                                        $badgeStyle = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                    }
                                @endphp
                                <span class="px-2.5 py-0.5 border text-xs font-semibold rounded-full {{ $badgeStyle }}">
                                    {{ $bin->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $bin->updated_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="trash-2" class="w-8 h-8 text-slate-300"></i>
                                    <span>Aucun bac ne correspond aux filtres de recherche.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($bins->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                    Affichage de <span class="font-semibold text-slate-700">{{ $bins->firstItem() }}</span>
                    à <span class="font-semibold text-slate-700">{{ $bins->lastItem() }}</span>
                    sur <span class="font-semibold text-slate-700">{{ $bins->total() }}</span> bac(s)
                </p>
                {{ $bins->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    <span class="font-semibold text-slate-700">{{ $bins->total() }}</span> bac(s) au total
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
