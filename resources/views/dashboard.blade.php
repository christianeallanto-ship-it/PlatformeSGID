@extends('layouts.app')

@section('title', 'Tableau de bord - BENINCLEAN')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 380px;
        width: 100%;
        border-radius: 14px;
        z-index: 1;
    }
    .donut-chart {
        position: relative;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }
    .donut-chart:hover {
        transform: rotate(5deg) scale(1.02);
    }
    .donut-hole {
        width: 110px;
        height: 110px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 4px 8px rgba(0,0,0,0.03);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.3);
        border-radius: 9999px;
    }
    .premium-card {
        background-color: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.05), 0 5px 10px -2px rgba(0, 0, 0, 0.03);
        border-color: rgba(226, 232, 240, 0.8);
    }
</style>

<!-- BEGIN: KPI Row (Minimalist and High-End design) -->
<section class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-6">
    <!-- KPI Card 1: Total Bacs -->
    <div class="premium-card p-5 rounded-2xl border-t-4 border-t-blue-500 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50/80 border border-blue-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="layers" class="w-6 h-6 text-blue-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Total des bacs</p>
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalBins }}</span>
            </div>
            <p class="text-[10px] text-slate-400 font-medium mt-1">Bacs enregistrés</p>
        </div>
    </div>

    <!-- KPI Card 2: Bacs Normaux -->
    <div class="premium-card p-5 rounded-2xl border-t-4 border-t-emerald-500 flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50/80 border border-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Bacs normaux</p>
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $normalBins }}</span>
            </div>
            <p class="text-[10px] text-emerald-600/80 font-bold mt-1">Niveau &lt; {{ $thresholdAlmostFull }}%</p>
        </div>
    </div>

    <!-- KPI Card 3: Bacs Presque Pleins -->
    <div class="premium-card p-5 rounded-2xl border-t-4 border-t-amber-500 flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-50/80 border border-amber-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Presque pleins</p>
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $almostFullBins }}</span>
            </div>
            <p class="text-[10px] text-amber-600/80 font-bold mt-1">Niveau {{ $thresholdAlmostFull }}-{{ $thresholdFull }}%</p>
        </div>
    </div>

    <!-- KPI Card 4: Bacs Pleins -->
    <div class="premium-card p-5 rounded-2xl border-t-4 border-t-rose-500 flex items-center gap-4">
        <div class="w-12 h-12 bg-rose-50/80 border border-rose-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="alert-circle" class="w-6 h-6 text-rose-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Bacs pleins</p>
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-2xl font-black text-slate-800 tracking-tight text-rose-600">{{ $fullBins }}</span>
            </div>
            <p class="text-[10px] text-rose-600/80 font-bold mt-1">Niveau &gt;= {{ $thresholdFull }}%</p>
        </div>
    </div>

    <!-- KPI Card 5: Collectes -->
    <div class="premium-card p-5 rounded-2xl border-t-4 border-t-purple-500 flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-50/80 border border-purple-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="truck" class="w-6 h-6 text-purple-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Collectes</p>
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalCollections }}</span>
            </div>
            <p class="text-[10px] text-slate-400 font-medium mt-1">Tournées au total</p>
        </div>
    </div>
</section>
<!-- END: KPI Row -->

@if (session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm animate-pulse">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Column: Map and Charts -->
    <div class="lg:col-span-8 space-y-6">
        <!-- Map Panel -->
        <section class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 flex items-center gap-2.5 text-base">
                    <i data-lucide="map" class="w-5 h-5 text-emerald-500"></i>
                    Carte de monitoring en temps réel
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('map') }}" class="text-xs text-indigo-600 font-bold hover:text-indigo-800 flex items-center gap-1">
                        <span>Agrandir la carte</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
            <div class="p-3 bg-slate-50/50">
                <div id="map"></div>
            </div>
            <div class="bg-white px-5 py-4 border-t border-slate-100 flex justify-center gap-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                    <span>Normal (&lt; {{ $thresholdAlmostFull }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 ring-4 ring-amber-50"></span>
                    <span>Presque plein ({{ $thresholdAlmostFull }}-{{ $thresholdFull }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-4 ring-rose-50"></span>
                    <span>Plein (&gt;= {{ $thresholdFull }}%)</span>
                </div>
            </div>
        </section>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Donut Chart -->
            <section class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <h2 class="font-bold text-slate-800 text-sm uppercase tracking-wider">État de remplissage des bacs</h2>
                    <p class="text-xs text-slate-400 mt-1">Répartition actuelle par niveau de remplissage</p>
                </div>
                <div class="flex flex-col items-center gap-6 mt-6">
                    @php
                        $normalPct = $totalBins > 0 ? round(($normalBins / $totalBins) * 100, 1) : 0;
                        $almostPct = $totalBins > 0 ? round(($almostFullBins / $totalBins) * 100, 1) : 0;
                        $fullPct = $totalBins > 0 ? round(($fullBins / $totalBins) * 100, 1) : 0;
                        
                        $greenEnd = $normalPct;
                        $yellowEnd = $normalPct + $almostPct;
                    @endphp
                    <div class="donut-chart shrink-0" style="background: conic-gradient(#10b981 0% {{ $greenEnd }}%, #f59e0b {{ $greenEnd }}% {{ $yellowEnd }}%, #f43f5e {{ $yellowEnd }}% 100%);">
                        <div class="donut-hole">
                            <span class="text-3xl font-black text-slate-800 leading-none">{{ $totalBins }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Bacs</span>
                        </div>
                    </div>
                    <div class="w-full space-y-3">
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-1 whitespace-nowrap">
                                <span class="text-slate-650 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Normaux (&lt; {{ $thresholdAlmostFull }}%)</span>
                                <span class="text-slate-800 font-bold">{{ $normalBins }} ({{ $normalPct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-green-500 h-2 rounded-full" style="width:{{ $normalPct }}%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-1 whitespace-nowrap">
                                <span class="text-slate-650 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Presque pleins ({{ $thresholdAlmostFull }}–{{ $thresholdFull }}%)</span>
                                <span class="text-slate-800 font-bold">{{ $almostFullBins }} ({{ $almostPct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-yellow-500 h-2 rounded-full" style="width:{{ $almostPct }}%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-1 whitespace-nowrap">
                                <span class="text-slate-650 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Pleins (&gt;= {{ $thresholdFull }}%)</span>
                                <span class="text-rose-650 font-bold">{{ $fullBins }} ({{ $fullPct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-rose-500 h-2 rounded-full" style="width:{{ $fullPct }}%"></div></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Line Chart (Chart.js) -->
            <section class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <div>
                    <h2 class="font-bold text-slate-800 mb-1 text-sm uppercase tracking-wider">Niveaux de remplissage</h2>
                    <p class="text-xs text-slate-400 mt-1">Suivi de la moyenne quotidienne globale du taux de remplissage</p>
                </div>
                <div class="relative flex-grow min-h-[245px] mt-3 w-full">
                    <canvas id="chartFillEvolution"></canvas>
                </div>
            </section>
        </div>
    </div>

    <!-- Right Column: Recent Alerts & Scheduled Collections -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Recent Alerts -->
        <section class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-5 flex items-center justify-between border-b border-slate-100">
                <h2 class="font-bold text-slate-800 text-base">Alertes actives ({{ $totalAlerts }})</h2>
                <a class="text-indigo-600 text-xs font-bold hover:underline" href="{{ route('alerts.index') }}">Voir toutes</a>
            </div>
            <div class="divide-y divide-slate-50 max-h-[380px] overflow-y-auto custom-scrollbar">
                @forelse($recentAlerts as $alert)
                    <div class="p-4 flex items-start gap-4 hover:bg-slate-50/50 transition-colors border-l-4 {{ $alert->bin->fill_level >= 80 ? 'border-l-rose-500' : 'border-l-amber-500' }}">
                        <div class="mt-1 flex-shrink-0">
                            <div class="w-10 h-10 {{ $alert->bin->fill_level >= 80 ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }} rounded-xl flex items-center justify-center">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-xs font-bold text-slate-850 truncate">
                                    Bac {{ $alert->bin->code }}
                                </h3>
                                <span class="text-[9px] text-slate-400 shrink-0 font-medium">{{ $alert->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $alert->bin->location }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[10px] px-2 py-0.5 {{ $alert->bin->fill_level >= 80 ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }} rounded-md font-bold">{{ $alert->bin->fill_level }}% rempli</span>
                                <form method="POST" action="{{ route('alerts.resolve', $alert) }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] px-2.5 py-1 text-emerald-600 font-extrabold border border-emerald-200 hover:bg-emerald-50 rounded-lg hover:border-emerald-300 transition-all flex items-center gap-1 shadow-sm">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        Vider
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Aucune alerte active en ce moment.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Scheduled Collections -->
        <section class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-5 flex items-center justify-between border-b border-slate-100">
                <h2 class="font-bold text-slate-800 text-base">Prochaines collectes</h2>
                <a class="text-indigo-600 text-xs font-bold hover:underline" href="{{ route('collections.index') }}">Voir toutes</a>
            </div>
            <div class="p-4 space-y-3.5">
                @forelse($upcomingCollections as $collection)
                    <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/50 rounded-2xl border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <div class="w-10 h-10 bg-purple-50 text-purple-650 rounded-xl flex items-center justify-center shrink-0 border border-purple-100">
                            <i data-lucide="truck" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xs font-bold text-slate-800 truncate">{{ $collection->route_name }}</h3>
                            <p class="text-[10px] text-slate-500 truncate">Chauffeur: <span class="font-semibold text-slate-650">{{ $collection->driver_name }}</span></p>
                            <p class="text-[9px] text-slate-400 font-bold mt-1 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                <span>{{ $collection->scheduled_at->format('d/m/Y H:i') }}</span>
                            </p>
                        </div>
                        <span class="px-2.5 py-0.5 {{ $collection->status === 'Terminée' ? 'bg-slate-200 text-slate-700' : ($collection->status === 'En cours' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-750') }} text-[9px] font-black rounded-full uppercase tracking-wide shrink-0">
                            {{ $collection->status }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Aucune tournée planifiée.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<!-- Leaflet Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dynamic central position based on chosen city
        const map = L.map('map').setView([{{ $center['lat'] }}, {{ $center['lng'] }}], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const bins = @json($bins);

        bins.forEach(function (bin) {
            if (bin.latitude && bin.longitude) {
                let color = '#10b981'; // Green (emerald-500)
                if (bin.fill_level >= {{ $thresholdFull }}) {
                    color = '#f43f5e'; // Red (rose-500)
                } else if (bin.fill_level >= {{ $thresholdAlmostFull }}) {
                    color = '#f59e0b'; // Yellow (amber-500)
                }

                const circle = L.circle([bin.latitude, bin.longitude], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.55,
                    radius: 90
                }).addTo(map);

                let tempText = bin.temperature !== null ? `${parseFloat(bin.temperature).toFixed(1)} °C` : '--';
                let airText = bin.air_quality !== null ? `${bin.air_quality} ppm` : '--';

                const popupContent = `
                    <div class="p-1 font-sans">
                        <p class="font-bold text-slate-800 border-b pb-1 mb-1 text-sm">Bac ${bin.code}</p>
                        <p class="text-xs"><strong>Adresse :</strong> ${bin.location}</p>
                        <p class="text-xs"><strong>Remplissage :</strong> ${bin.fill_level}%</p>
                        <p class="text-xs"><strong>Température :</strong> ${tempText}</p>
                        <p class="text-xs"><strong>Qualité de l'air :</strong> ${airText}</p>
                        <span class="inline-block mt-1.5 px-2 py-0.5 text-[9px] font-bold text-white rounded uppercase tracking-wide" style="background-color: ${color};">${bin.status}</span>
                    </div>
                `;
                circle.bindPopup(popupContent);
            }
        });

        // Chart.js - Évolution des niveaux de remplissage
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
                layout: {
                    padding: {
                        bottom: 12
                    }
                },
                plugins: {
                    legend: { 
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 6,
                            padding: 16,
                            font: { size: 9 }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { stepSize: 5, autoSkip: false }
                    }
                }
            }
        });
    });
</script>
@endsection