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
        @if(auth()->user()->role === 'Administrateur')
            <div class="shrink-0">
                <button onclick="document.getElementById('modalAddBin').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-md transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Ajouter un bac
                </button>
            </div>
        @endif
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

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
                    <option value="Normal" {{ request('status') === 'Normal' ? 'selected' : '' }}>Normal (&lt; {{ $thresholdAlmostFull }}%)</option>
                    <option value="Presque plein" {{ request('status') === 'Presque plein' ? 'selected' : '' }}>Presque plein ({{ $thresholdAlmostFull }}-{{ $thresholdFull }}%)</option>
                    <option value="Plein" {{ request('status') === 'Plein' ? 'selected' : '' }}>Plein (&gt;= {{ $thresholdFull }}%)</option>
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
                        <th class="px-6 py-4">Température</th>
                        <th class="px-6 py-4">Qualité de l'air</th>
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
                            <td class="px-6 py-4">
                                @if($bin->temperature !== null)
                                    <span class="font-semibold text-slate-700">{{ number_format($bin->temperature, 1) }} °C</span>
                                @else
                                    <span class="text-slate-400">--</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($bin->air_quality !== null)
                                    @php
                                        $airStyle = 'text-green-600 bg-green-50';
                                        if ($bin->air_quality > 1000) {
                                            $airStyle = 'text-red-600 bg-red-50';
                                        } elseif ($bin->air_quality > 600) {
                                            $airStyle = 'text-yellow-600 bg-yellow-50';
                                        }
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $airStyle }}">{{ $bin->air_quality }} ppm</span>
                                @else
                                    <span class="text-slate-400">--</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 w-64">
                                <div class="flex items-center gap-3">
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        @php
                                            $barColor = 'bg-green-500';
                                            if ($bin->fill_level >= $thresholdFull) {
                                                $barColor = 'bg-red-500';
                                            } elseif ($bin->fill_level >= $thresholdAlmostFull) {
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
                                    if ($bin->fill_level >= $thresholdFull) {
                                        $badgeStyle = 'bg-red-50 text-red-700 border-red-200';
                                    } elseif ($bin->fill_level >= $thresholdAlmostFull) {
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
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
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
    <!-- Modal Ajouter un Bac -->
    @if(auth()->user()->role === 'Administrateur')
    <div id="modalAddBin" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-100 transform transition-all duration-300">
            <div class="bg-green-600 px-6 py-4 flex items-center justify-between text-white">
                <h3 class="font-bold text-base flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Ajouter un nouveau bac
                </h3>
                <button onclick="document.getElementById('modalAddBin').classList.add('hidden')" class="text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('bins.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-xs font-bold text-slate-500 uppercase mb-1">Code du bac</label>
                        <input type="text" name="code" id="code" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="ex: B101">
                    </div>
                    <div>
                        <label for="type" class="block text-xs font-bold text-slate-500 uppercase mb-1">Type de déchet</label>
                        <select name="type" id="type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                            <option value="Tout-venant">Tout-venant</option>
                            <option value="Organique">Organique</option>
                            <option value="Plastique">Plastique</option>
                            <option value="Verre">Verre</option>
                            <option value="Papier">Papier</option>
                            <option value="Métal">Métal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="location" class="block text-xs font-bold text-slate-500 uppercase mb-1">Localisation / Adresse</label>
                    <input type="text" name="location" id="location" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="ex: Akpakpa Stade, face pharmacie">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-xs font-bold text-slate-500 uppercase mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" required value="{{ $center['lat'] }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="longitude" class="block text-xs font-bold text-slate-500 uppercase mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" required value="{{ $center['lng'] }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="fill_level" class="block text-xs font-bold text-slate-500 uppercase mb-1">Remplissage (%)</label>
                        <input type="number" name="fill_level" id="fill_level" min="0" max="100" value="0" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="temperature" class="block text-xs font-bold text-slate-500 uppercase mb-1">Température (°C)</label>
                        <input type="number" step="0.1" name="temperature" id="temperature" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="ex: 28.5">
                    </div>
                    <div>
                        <label for="air_quality" class="block text-xs font-bold text-slate-500 uppercase mb-1">Qualité air (ppm)</label>
                        <input type="number" name="air_quality" id="air_quality" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500" placeholder="ex: 400">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAddBin').classList.add('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition-all">
                        Annuler
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all">
                        Ajouter le bac
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
