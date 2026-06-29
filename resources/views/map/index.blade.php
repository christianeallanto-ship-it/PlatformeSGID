@extends('layouts.app')

@section('title', 'Carte de suivi en temps réel - BENINCLEAN')

@section('content')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map-large {
        height: 600px;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        z-index: 1;
    }
</style>

<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="map-pin" class="text-green-600"></i>
                Carte de monitoring en temps réel
            </h1>
            <p class="text-sm text-slate-500">Visualisez la localisation géographique exacte de tous les bacs connectés et leur niveau de remplissage actuel.</p>
        </div>
        <div class="flex gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-600 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                Normal
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-600 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                Presque plein
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-600 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                Plein
            </span>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">
        <div id="map-large"></div>
        
        <!-- Legend Control -->
        <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur-md px-4 py-3 rounded-xl shadow-lg border border-slate-200 z-[1000] text-xs max-w-xs space-y-2">
            <h4 class="font-bold text-slate-800 border-b pb-1 mb-1">Légende</h4>
            <div class="space-y-1.5 font-medium text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                    <span>Niveau &lt; {{ $thresholdAlmostFull }}%</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span>
                    <span>Niveau {{ $thresholdAlmostFull }}% - {{ $thresholdFull }}%</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                    <span>Niveau &gt;= {{ $thresholdFull }}% (Alerte)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dynamic center based on selected city
        const map = L.map('map-large').setView([{{ $center['lat'] }}, {{ $center['lng'] }}], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const bins = @json($bins);

        bins.forEach(function (bin) {
            if (bin.latitude && bin.longitude) {
                let color = '#28a745'; // Green
                if (bin.fill_level >= {{ $thresholdFull }}) {
                    color = '#dc3545'; // Red
                } else if (bin.fill_level >= {{ $thresholdAlmostFull }}) {
                    color = '#ffc107'; // Yellow
                }

                // Custom marker using L.circle
                const circle = L.circle([bin.latitude, bin.longitude], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.6,
                    radius: 100 // Radius in meters
                }).addTo(map);

                const popupContent = `
                    <div class="p-2 font-sans w-52">
                        <div class="flex justify-between items-center border-b pb-1 mb-2">
                            <span class="font-bold text-slate-800 text-sm">Bac ${bin.code}</span>
                            <span class="text-[10px] font-bold text-white px-2 py-0.5 rounded" style="background-color: ${color};">${bin.status}</span>
                        </div>
                        <p class="text-xs text-slate-600 mb-1"><strong>Lieu :</strong> ${bin.location}</p>
                        <p class="text-xs text-slate-600 mb-2"><strong>Remplissage :</strong> ${bin.fill_level}%</p>
                        
                        <div class="pt-2 border-t flex justify-between items-center">
                            <a href="{{ route('bins.index') }}?search=${bin.code}" class="text-[10px] text-blue-600 font-bold hover:underline flex items-center gap-0.5">
                                Voir détails
                            </a>
                            <span class="text-[9px] text-slate-400">Màj: ${new Date(bin.updated_at).toLocaleDateString('fr-FR')}</span>
                        </div>
                    </div>
                `;
                circle.bindPopup(popupContent);
            }
        });
    });
</script>
@endsection
