@extends('layouts.app')

@section('title', 'Rapports & Statistiques - BENINCLEAN')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<style>
    @media print {
        @page {
            margin: 2cm 1.5cm 2cm 1.5cm;
        }

        .print-header-container {
            display: flex !important;
            position: fixed;
            top: -1.2cm;
            left: 0;
            right: 0;
            width: 100%;
        }

        .print-footer-container {
            display: flex !important;
            position: fixed;
            bottom: -1.2cm;
            left: 0;
            right: 0;
            width: 100%;
        }

        /* Masquer la navigation et les contrôles non nécessaires à l'impression */
        aside, header, form, button, .no-print, .flex-row-btn {
            display: none !important;
        }
        
        /* Ajuster la zone de contenu principale */
        body {
            counter-reset: page;
            background-color: white !important;
            color: black !important;
            font-size: 12px !important;
        }

        .print-page-num::after {
            counter-increment: page;
            content: counter(page);
        }
        
        main, .flex-1, .p-6, .overflow-y-auto {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            width: 100% !important;
        }

        .bg-white {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            border-radius: 8px !important;
            page-break-inside: avoid;
        }

        .grid {
            display: grid !important;
        }

        .page-break {
            page-break-before: always;
        }
    }
</style>

<div class="space-y-6 print-container">
    <!-- En-tête personnalisé pour l'impression (masqué à l'écran, visible à l'impression) -->
    <div class="hidden print-header-container">
        <div class="flex justify-between items-center text-[10px] text-slate-500 border-b border-slate-200 pb-2 mb-6 w-full font-sans">
            <span class="font-bold text-slate-800">BENINCLEAN — RAPPORT D'ACTIVITÉ</span>
            <span class="font-semibold text-slate-700">Période : @if($startDate && $endDate) du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @else Toutes dates @endif</span>
            <span>Généré le : {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <!-- Pied de page personnalisé pour l'impression (masqué à l'écran, visible à l'impression) -->
    <div class="hidden print-footer-container">
        <div class="flex justify-between items-center text-[10px] text-slate-500 border-t border-slate-200 pt-2 w-full font-sans">
            <span>&copy; {{ date('Y') }} BENINCLEAN — Interface de monitoring</span>
            <span>Page <span class="print-page-num"></span></span>
        </div>
    </div>
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="text-green-600"></i>
                Rapports d'activité &amp; Statistiques
            </h1>
            <p class="text-sm text-slate-500">
                @if($startDate && $endDate)
                    Analyse des statistiques et historique pour la période du <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span> au <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>.
                @else
                    Analysez l'efficacité des collectes, suivez l'état général des infrastructures et imprimez vos bilans.
                @endif
            </p>
        </div>
        
        <!-- Print Button -->
        <div class="flex gap-2 shrink-0 no-print">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-4 rounded-xl flex items-center gap-2 shadow-md transition-all">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Imprimer le rapport
            </button>
        </div>
    </div>

    <!-- Date Range Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm no-print">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date de début</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 py-2">
                </div>
                <div>
                    <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date de fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-green-500 focus:border-green-500 py-2">
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-6 rounded-xl flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filtrer la période
                </button>
                @if($startDate || $endDate)
                    <a href="{{ route('reports.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold py-2 px-4 rounded-xl flex items-center justify-center transition-all">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Taux de remplissage moyen -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Remplissage Moyen</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $averageFillLevel }}%</span>
            </div>
            <div class="mt-3 bg-slate-100 rounded-full h-1.5 w-full">
                <div class="bg-green-500 h-1.5 rounded-full transition-all duration-700" style="width: {{ $averageFillLevel }}%"></div>
            </div>
        </div>

        <!-- Taux résolution alertes -->
        @php $resolutionRate = $totalAlerts > 0 ? round(($resolvedAlerts / $totalAlerts) * 100, 1) : 0; @endphp
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Résolution des Alertes</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $resolutionRate }}%</span>
                <span class="text-xs text-slate-500">({{ $resolvedAlerts }}/{{ $totalAlerts }})</span>
            </div>
            <div class="mt-3 bg-slate-100 rounded-full h-1.5 w-full">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-700" style="width: {{ $resolutionRate }}%"></div>
            </div>
        </div>

        <!-- Taux de tournées terminées -->
        @php $collectionRate = $totalCollections > 0 ? round(($completedCollections / $totalCollections) * 100, 1) : 0; @endphp
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tournées Terminées</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $collectionRate }}%</span>
                <span class="text-xs text-slate-500">({{ $completedCollections }}/{{ $totalCollections }})</span>
            </div>
            <div class="mt-3 bg-slate-100 rounded-full h-1.5 w-full">
                <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-700" style="width: {{ $collectionRate }}%"></div>
            </div>
        </div>

        <!-- Alertes actives -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alertes Actives</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-red-650">{{ $activeAlerts }}</span>
                <span class="text-xs text-slate-500">nécessitant action</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 font-medium">Bacs au-dessus du seuil défini</p>
        </div>
    </div>

    <!-- Graphiques dynamiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. Graphique en anneau : État des bacs -->
        <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="font-bold text-slate-800 text-lg mb-1">État de remplissage des bacs</h2>
            <p class="text-xs text-slate-400 mb-5">Répartition actuelle par niveau de remplissage</p>
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <div class="relative w-48 h-48 shrink-0">
                    <canvas id="chartBinsStatus" width="192" height="192"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $totalBins }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Bacs total</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3 w-full">
                    @php
                        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
                        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);

                        $normalPct  = $totalBins > 0 ? round(($normalBins / $totalBins) * 100, 1) : 0;
                        $almostPct  = $totalBins > 0 ? round(($almostFullBins / $totalBins) * 100, 1) : 0;
                        $fullPct    = $totalBins > 0 ? round(($fullBins / $totalBins) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-600 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Normal (&lt; {{ $thresholdAlmostFull }}%)</span>
                            <span class="text-slate-800">{{ $normalBins }} ({{ $normalPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-green-500 h-2 rounded-full" style="width:{{ $normalPct }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-600 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Presque plein ({{ $thresholdAlmostFull }}–{{ $thresholdFull }}%)</span>
                            <span class="text-slate-800">{{ $almostFullBins }} ({{ $almostPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-yellow-500 h-2 rounded-full" style="width:{{ $almostPct }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-600 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Plein (&gt;= {{ $thresholdFull }}%)</span>
                            <span class="text-slate-800">{{ $fullBins }} ({{ $fullPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-red-500 h-2 rounded-full" style="width:{{ $fullPct }}%"></div></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Graphique courbe : Évolution des niveaux de remplissage -->
        <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="font-bold text-slate-800 text-lg mb-1">Niveaux de remplissage</h2>
            <p class="text-xs text-slate-400 mb-5">Suivi de la moyenne quotidienne globale du taux de remplissage</p>
            <div class="relative" style="height: 200px;">
                <canvas id="chartFillEvolution"></canvas>
            </div>
        </section>
    </div>

    <!-- 3. Collectes & Alertes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Collectes -->
        <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="font-bold text-slate-800 text-lg mb-1 flex items-center gap-2">
                <i data-lucide="truck" class="text-purple-500 w-5 h-5"></i>
                Statistiques des Collectes
            </h2>
            <p class="text-xs text-slate-400 mb-5">Répartition des collectes par statut sur la période</p>
            <div class="relative" style="height: 200px;">
                <canvas id="chartCollections"></canvas>
            </div>
        </section>

        <!-- Alertes -->
        <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="font-bold text-slate-800 text-lg mb-1 flex items-center gap-2">
                <i data-lucide="bell" class="text-red-500 w-5 h-5"></i>
                Historique des Alertes
            </h2>
            <p class="text-xs text-slate-400 mb-5">Alertes créées et résolues durant l'intervalle</p>
            <div class="relative" style="height: 200px;">
                <canvas id="chartAlerts"></canvas>
            </div>
        </section>
    </div>

    <!-- 4. Tableau de l'historique détaillé -->
    <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div>
                <h2 class="font-bold text-slate-800 text-lg">Historique détaillé des alertes</h2>
                <p class="text-xs text-slate-400">Journal de toutes les alertes déclenchées</p>
            </div>
            <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full no-print">
                {{ $alertsHistory->count() }} alertes enregistrées
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Date / Heure</th>
                        <th class="px-6 py-3">Code Bac</th>
                        <th class="px-6 py-3">Adresse / Localisation</th>
                        <th class="px-6 py-3">Message de l'alerte</th>
                        <th class="px-6 py-3">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($alertsHistory as $alert)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3 font-semibold text-slate-700">{{ $alert->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 font-bold text-slate-800">{{ $alert->bin->code ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-xs">{{ $alert->bin->location ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-xs">{{ $alert->message }}</td>
                            <td class="px-6 py-3">
                                @if($alert->is_resolved)
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 rounded-full inline-block">Résolue (Vidé)</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 rounded-full inline-block">Active</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Aucune alerte enregistrée pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Options communes
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#64748b'; // slate-500

    const gridColor  = 'rgba(148,163,184,0.15)';
    const tickColor  = '#94a3b8';

    // 1. Anneau : état des bacs
    new Chart(document.getElementById('chartBinsStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Normal (< {{ $thresholdAlmostFull }}%)', 'Presque plein ({{ $thresholdAlmostFull }}–{{ $thresholdFull }}%)', 'Plein (>= {{ $thresholdFull }}%)'],
            datasets: [{
                data: [{{ $normalBins }}, {{ $almostFullBins }}, {{ $fullBins }}],
                backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                borderColor: ['#059669', '#d97706', '#e11d48'],
                borderWidth: 2,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '72%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label} : ${ctx.raw} bac(s) (${ctx.parsed ? Math.round(ctx.parsed / {{ $totalBins > 0 ? $totalBins : 1 }} * 100) : 0}%)`
                    }
                }
            }
        }
    });

    // 2. Courbe : Évolution des niveaux de remplissage (remplace type de déchet)
    const evolutionLabels = @json($chartLabels);
    const evolutionNormal = @json($chartNormal);
    const evolutionAlmost = @json($chartAlmostFull);
    const evolutionFull   = @json($chartFull);

    new Chart(document.getElementById('chartFillEvolution'), {
        type: 'line',
        data: {
            labels: evolutionLabels,
            datasets: [
                {
                    label: 'Normaux (< {{ $thresholdAlmostFull }}%)',
                    data: evolutionNormal,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    pointBackgroundColor: '#10b981'
                },
                {
                    label: 'Presque pleins ({{ $thresholdAlmostFull }}-{{ $thresholdFull }}%)',
                    data: evolutionAlmost,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.05)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    pointBackgroundColor: '#f59e0b'
                },
                {
                    label: 'Pleins (>= {{ $thresholdFull }}%)',
                    data: evolutionFull,
                    borderColor: '#f43f5e',
                    backgroundColor: 'rgba(244, 63, 94, 0.05)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    pointBackgroundColor: '#f43f5e'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 6,
                        padding: 10,
                        font: { size: 9 }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: tickColor }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: tickColor,
                        stepSize: 5
                    },
                    border: { dash: [4, 4] }
                }
            }
        }
    });

    // 3. Barres : tournées par statut
    new Chart(document.getElementById('chartCollections'), {
        type: 'bar',
        data: {
            labels: ['Planifiées', 'En cours', 'Terminées'],
            datasets: [{
                label: 'Tournées',
                data: [{{ $scheduledCollections }}, {{ $ongoingCollections }}, {{ $completedCollections }}],
                backgroundColor: ['#cbd5e1', '#f59e0b', '#10b981'],
                borderColor:     ['#94a3b8', '#d97706', '#059669'],
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#334155', font: { weight: '600' } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: tickColor,
                        stepSize: 1,
                        precision: 0
                    },
                    border: { dash: [4, 4] }
                }
            }
        }
    });

    // 4. Barres : alertes par statut
    new Chart(document.getElementById('chartAlerts'), {
        type: 'bar',
        data: {
            labels: ['Total', 'Actives', 'Résolues'],
            datasets: [{
                label: 'Alertes',
                data: [{{ $totalAlerts }}, {{ $activeAlerts }}, {{ $resolvedAlerts }}],
                backgroundColor: ['#cbd5e1', '#f43f5e', '#10b981'],
                borderColor:     ['#94a3b8', '#e11d48', '#059669'],
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#334155', font: { weight: '600' } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: tickColor,
                        stepSize: 1,
                        precision: 0
                    },
                    border: { dash: [4, 4] }
                }
            }
        }
    });
});
</script>
@endsection
