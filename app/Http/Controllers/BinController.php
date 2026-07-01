<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;

class BinController extends Controller
{
    /**
     * Afficher la liste des bacs avec filtres.
     */
    public function index(Request $request)
    {
        $query = Bin::query();

        // Filtrage par statut (Normal, Presque plein, Plein)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        // Recherche par code ou localisation
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination à 10 éléments par page
        $bins = $query->orderBy('code')->paginate(10)->withQueryString();

        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);
        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');

        $villes = [
            'Cotonou'       => ['lat' => 6.3703,  'lng' => 2.4308],
            'Porto-Novo'    => ['lat' => 6.4969,  'lng' => 2.6283],
            'Parakou'       => ['lat' => 9.3370,  'lng' => 2.6277],
            'Abomey-Calavi' => ['lat' => 6.4490,  'lng' => 2.3554],
            'Bohicon'       => ['lat' => 7.1781,  'lng' => 2.0717],
            'Natitingou'    => ['lat' => 10.3103, 'lng' => 1.3786],
            'Ouidah'        => ['lat' => 6.3612,  'lng' => 2.0854],
            'Lokossa'       => ['lat' => 6.6384,  'lng' => 1.7173],
            'Djougou'       => ['lat' => 9.7097,  'lng' => 1.6660],
            'Kandi'         => ['lat' => 11.1344, 'lng' => 2.9389],
        ];
        $center = $villes[$mapCity] ?? $villes['Cotonou'];

        return view('bins.index', compact('bins', 'mapCity', 'thresholdAlmostFull', 'thresholdFull', 'center'));
    }

    /**
     * Recevoir les données télémétriques d'un bac (IoT PlatformIO / ESP32).
     */
    public function updateTelemetry(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'fill_level' => 'required|integer|min:0|max:100',
            'temperature' => 'nullable|numeric',
            'air_quality' => 'nullable|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');

        // Déterminer la latitude et la longitude à utiliser
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $lat = (float) $validated['latitude'];
            $lng = (float) $validated['longitude'];
        } else {
            // Fallback sur le centre de la ville active + offset aléatoire
            $villes = [
                'Cotonou'       => ['lat' => 6.3703,  'lng' => 2.4308],
                'Porto-Novo'    => ['lat' => 6.4969,  'lng' => 2.6283],
                'Parakou'       => ['lat' => 9.3370,  'lng' => 2.6277],
                'Abomey-Calavi' => ['lat' => 6.4490,  'lng' => 2.3554],
                'Bohicon'       => ['lat' => 7.1781,  'lng' => 2.0717],
                'Natitingou'    => ['lat' => 10.3103, 'lng' => 1.3786],
                'Ouidah'        => ['lat' => 6.3612,  'lng' => 2.0854],
                'Lokossa'       => ['lat' => 6.6384,  'lng' => 1.7173],
                'Djougou'       => ['lat' => 9.7097,  'lng' => 1.6660],
                'Kandi'         => ['lat' => 11.1344, 'lng' => 2.9389],
            ];
            $center = $villes[$mapCity] ?? $villes['Cotonou'];
            $lat = $center['lat'] + (mt_rand(-20000, 20000) / 1000000);
            $lng = $center['lng'] + (mt_rand(-35000, 35000) / 1000000);
        }

        $bin = Bin::firstOrNew(
            ['code' => $validated['code']],
            [
                'location' => 'Bac connecté IOT (' . $mapCity . ')',
                'latitude' => $lat,
                'longitude' => $lng,
                'type' => 'Tout-venant',
            ]
        );

        // Si le bac existait déjà et que de nouvelles coordonnées GPS sont envoyées, on les met à jour
        if ($bin->exists && $request->filled('latitude') && $request->filled('longitude')) {
            $bin->latitude = $lat;
            $bin->longitude = $lng;
        }

        $bin->fill_level = $validated['fill_level'];
        $bin->temperature = $validated['temperature'] ?? $bin->temperature;
        $bin->air_quality = $validated['air_quality'] ?? $bin->air_quality;

        // Recalculer le statut en fonction des seuils de configuration
        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);

        if ($bin->fill_level >= $thresholdFull) {
            $bin->status = 'Plein';
        } elseif ($bin->fill_level >= $thresholdAlmostFull) {
            $bin->status = 'Presque plein';
        } else {
            $bin->status = 'Normal';
        }
        $bin->save();

        // Gérer les alertes
        if ($bin->fill_level >= $thresholdAlmostFull) {
            $statusText = ($bin->fill_level >= $thresholdFull) ? 'plein' : 'presque plein';
            $alert = \App\Models\Alert::where('bin_id', $bin->id)->where('is_resolved', false)->first();
            if (!$alert) {
                \App\Models\Alert::create([
                    'bin_id' => $bin->id,
                    'message' => "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusText} ({$bin->fill_level}%).",
                    'is_resolved' => false,
                ]);
            } else {
                $alert->message = "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusText} ({$bin->fill_level}%).";
                $alert->save();
            }
        } else {
            // Résoudre l'alerte active si le niveau est repassé en dessous du seuil
            \App\Models\Alert::where('bin_id', $bin->id)->where('is_resolved', false)->update(['is_resolved' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => "Données du bac {$bin->code} mises à jour avec succès.",
            'bin' => [
                'code' => $bin->code,
                'fill_level' => $bin->fill_level,
                'temperature' => $bin->temperature,
                'air_quality' => $bin->air_quality,
                'latitude' => $bin->latitude,
                'longitude' => $bin->longitude,
                'status' => $bin->status,
            ]
        ]);
    }

    /**
     * Enregistrer manuellement un nouveau bac.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:bins,code',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|string|max:255',
            'fill_level' => 'nullable|integer|min:0|max:100',
            'temperature' => 'nullable|numeric',
            'air_quality' => 'nullable|integer',
        ], [
            'code.required' => 'Le code du bac est obligatoire.',
            'code.unique' => 'Ce code de bac est déjà utilisé.',
            'location.required' => 'La localisation est obligatoire.',
            'latitude.required' => 'La latitude est obligatoire.',
            'longitude.required' => 'La longitude est obligatoire.',
            'type.required' => 'Le type de déchet est obligatoire.',
        ]);

        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);

        $fillLevel = $validated['fill_level'] ?? 0;
        
        if ($fillLevel >= $thresholdFull) {
            $status = 'Plein';
        } elseif ($fillLevel >= $thresholdAlmostFull) {
            $status = 'Presque plein';
        } else {
            $status = 'Normal';
        }

        Bin::create([
            'code' => $validated['code'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'type' => $validated['type'],
            'fill_level' => $fillLevel,
            'temperature' => $validated['temperature'] ?? null,
            'air_quality' => $validated['air_quality'] ?? null,
            'status' => $status,
        ]);

        return redirect()->route('bins.index')->with('success', "Le bac {$validated['code']} a été ajouté avec succès.");
    }
}
