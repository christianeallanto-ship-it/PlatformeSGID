@extends('layouts.app')

@section('title', 'Gestion des Alertes - BENINCLEAN')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="bell" class="text-red-500"></i>
            Suivi des Alertes actives & résolues
        </h1>
        <p class="text-sm text-slate-500">Gérez les alertes automatiques déclenchées lorsque les bacs dépassent les seuils de remplissage de 60%.</p>
    </div>

    <!-- Feedback messages -->
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('alerts.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-slate-500">Filtrer par :</span>
                
                <a href="{{ route('alerts.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request()->filled('status') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-colors">
                    Toutes
                </a>
                
                <a href="{{ route('alerts.index', ['status' => 'active']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'active' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-colors">
                    Actives
                </a>
                
                <a href="{{ route('alerts.index', ['status' => 'resolved']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'resolved' ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-colors">
                    Résolues
                </a>
            </div>
        </form>
    </div>

    <!-- Alerts List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Bac</th>
                        <th class="px-6 py-4">Localisation / Adresse</th>
                        <th class="px-6 py-4">Message d'alerte</th>
                        <th class="px-6 py-4">Déclenchée le</th>
                        <th class="px-6 py-4">État</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($alerts as $alert)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                @if($alert->bin)
                                    <span class="px-2 py-1 bg-slate-100 text-slate-800 rounded font-mono text-xs">
                                        {{ $alert->bin->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Inconnu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($alert->bin)
                                    <span class="text-slate-700 font-medium">{{ $alert->bin->location }}</span>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-600">{{ $alert->message }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $alert->created_at->format('d/m/Y H:i') }} 
                                <span class="text-[10px] block">({{ $alert->created_at->diffForHumans() }})</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($alert->is_resolved)
                                    <span class="px-2.5 py-0.5 inline-flex items-center gap-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Résolue
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex items-center gap-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(!$alert->is_resolved)
                                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold shadow-sm transition-colors flex items-center gap-1 inline-flex ml-auto">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            Vider le bac
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Aucune action requise</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="bell-off" class="w-8 h-8 text-slate-300"></i>
                                    <span>Aucune alerte trouvée.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($alerts->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                    Affichage de <span class="font-semibold text-slate-700">{{ $alerts->firstItem() }}</span>
                    à <span class="font-semibold text-slate-700">{{ $alerts->lastItem() }}</span>
                    sur <span class="font-semibold text-slate-700">{{ $alerts->total() }}</span> alerte(s)
                </p>
                {{ $alerts->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    <span class="font-semibold text-slate-700">{{ $alerts->total() }}</span> alerte(s) au total
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
