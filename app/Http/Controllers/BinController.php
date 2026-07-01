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
        return view('bins.index', compact('bins', 'mapCity', 'thresholdAlmostFull', 'thresholdFull'));
    }

    /**
     * Recevoir les données télémétriques d'un bac (IoT PlatformIO / ESP32).
     */
    public function updateTelemetry(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'fill_level' => 'required|integer|min:0|max:100',
        ]);

        // Si le bac n'existe pas, on le crée dynamiquement dans la ville active
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

        $bin = Bin::firstOrNew(
            ['code' => $validated['code']],
            [
                'location' => 'Bac connecté IOT (' . $mapCity . ')',
                'latitude' => $center['lat'] + (mt_rand(-20000, 20000) / 1000000),
                'longitude' => $center['lng'] + (mt_rand(-35000, 35000) / 1000000),
                'type' => 'Tout-venant',
            ]
        );

        $bin->fill_level = $validated['fill_level'];

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
                'status' => $bin->status,
            ]
        ]);
    }
}
