@extends('layouts.app')

@section('title', 'Paramètres - BENINCLEAN')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="settings" class="text-green-600"></i>
            Paramètres du système
        </h1>
        <p class="text-sm text-slate-500">Configurez les seuils d'alerte des capteurs connectés, la zone d'intervention et les préférences de l'application.</p>
    </div>

    <!-- Message de succès -->
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Formulaire unique pour tout sauvegarder -->
    <form method="POST" action="{{ route('settings.save') }}" class="space-y-6">
        @csrf

        <!-- Seuils de remplissage -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i data-lucide="bell" class="text-red-500"></i>
                    Seuils de Remplissage &amp; Capteurs
                </h2>
                <p class="text-xs text-slate-400 mt-1">Déterminez les pourcentages de remplissage déclenchant les alertes automatiques.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Seuil presque plein -->
                    <div>
                        <label for="threshold_almost_full" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Seuil "Presque plein" (Orange)
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number"
                                   name="threshold_almost_full"
                                   id="threshold_almost_full"
                                   value="{{ old('threshold_almost_full', $settings['threshold_almost_full']) }}"
                                   min="40" max="79"
                                   class="text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 px-3 py-2 w-24">
                            <span class="text-sm font-semibold text-slate-600">% du volume total</span>
                        </div>
                        @error('threshold_almost_full')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Entre 40% et 79%</p>
                    </div>

                    <!-- Seuil plein -->
                    <div>
                        <label for="threshold_full" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Seuil "Plein" (Rouge)
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number"
                                   name="threshold_full"
                                   id="threshold_full"
                                   value="{{ old('threshold_full', $settings['threshold_full']) }}"
                                   min="80" max="100"
                                   class="text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 px-3 py-2 w-24">
                            <span class="text-sm font-semibold text-slate-600">% du volume total</span>
                        </div>
                        @error('threshold_full')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">Entre 80% et 100%</p>
                    </div>
                </div>

                <!-- Fréquence actualisation GPS -->
                <div>
                    <label for="gps_frequency" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Fréquence d'actualisation des données GPS
                    </label>
                    <select name="gps_frequency" id="gps_frequency"
                            class="text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2 w-64">
                        <option value="5"  {{ $settings['gps_frequency'] == 5  ? 'selected' : '' }}>Toutes les 5 minutes</option>
                        <option value="15" {{ $settings['gps_frequency'] == 15 ? 'selected' : '' }}>Toutes les 15 minutes</option>
                        <option value="30" {{ $settings['gps_frequency'] == 30 ? 'selected' : '' }}>Toutes les 30 minutes</option>
                        <option value="60" {{ $settings['gps_frequency'] == 60 ? 'selected' : '' }}>Chaque heure</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Zone d'intervention géographique -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i data-lucide="map-pin" class="text-blue-500"></i>
                    Zone d'intervention géographique
                </h2>
                <p class="text-xs text-slate-400 mt-1">Sélectionnez la ville principale sur laquelle la carte se centrera par défaut.</p>
            </div>
            <div class="p-6 space-y-4">
                <!-- Sélecteur de ville -->
                <div>
                    <label for="map_city" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ville principale d'intervention</label>
                    <select name="map_city" id="map_city"
                            class="text-sm border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500 py-2 w-full max-w-xs"
                            onchange="updateCoordinates(this.value)">
                        @php
                        $villes = [
                            'Tous'          => ['lat' => '8.5000', 'lng' => '2.3000'],
                            'Cotonou'       => ['lat' => '6.3703', 'lng' => '2.4308'],
                            'Porto-Novo'    => ['lat' => '6.4969', 'lng' => '2.6283'],
                            'Parakou'       => ['lat' => '9.3370', 'lng' => '2.6277'],
                            'Abomey-Calavi' => ['lat' => '6.4490', 'lng' => '2.3554'],
                            'Bohicon'       => ['lat' => '7.1781', 'lng' => '2.0717'],
                            'Natitingou'    => ['lat' => '10.3103','lng' => '1.3786'],
                            'Ouidah'        => ['lat' => '6.3612', 'lng' => '2.0854'],
                            'Lokossa'       => ['lat' => '6.6384', 'lng' => '1.7173'],
                            'Djougou'       => ['lat' => '9.7097', 'lng' => '1.6660'],
                            'Kandi'         => ['lat' => '11.1344','lng' => '2.9389'],
                        ];
                        @endphp
                        @foreach($villes as $ville => $coords)
                            <option value="{{ $ville }}"
                                    data-lat="{{ $coords['lat'] }}"
                                    data-lng="{{ $coords['lng'] }}"
                                    {{ $settings['map_city'] === $ville ? 'selected' : '' }}>
                                {{ $ville }}
                            </option>
                        @endforeach
                    </select>
                    @error('map_city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Aperçu coordonnées (lecture seule, mis à jour dynamiquement) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-md">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="text" id="display_lat" readonly
                               class="text-sm bg-slate-50 border-slate-200 text-slate-500 rounded-lg py-2 px-3 w-full cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="text" id="display_lng" readonly
                               class="text-sm bg-slate-50 border-slate-200 text-slate-500 rounded-lg py-2 px-3 w-full cursor-not-allowed">
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 font-medium italic">
                    Les coordonnées sont automatiquement renseignées selon la ville sélectionnée.
                </p>
            </div>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex justify-end">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 active:scale-95 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition-all shadow-sm flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Enregistrer tous les paramètres
            </button>
        </div>
    </form>
</div>

<script>
    // Coordonnées injectées depuis le serveur
    const villesCoords = @json($villes);

    function updateCoordinates(ville) {
        const select = document.getElementById('map_city');
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('display_lat').value = selectedOption.dataset.lat || '';
        document.getElementById('display_lng').value = selectedOption.dataset.lng || '';
    }

    // Initialiser avec la ville actuelle au chargement
    document.addEventListener('DOMContentLoaded', function () {
        updateCoordinates(document.getElementById('map_city').value);
    });
</script>
@endsection
