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

        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');
        return view('bins.index', compact('bins', 'mapCity'));
    }

    /**
     * Recevoir les données télémétriques d'un bac (IoT PlatformIO / ESP32).
     */
    public function updateTelemetry(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:bins,code',
            'fill_level' => 'required|integer|min:0|max:100',
        ]);

        $bin = Bin::where('code', $validated['code'])->firstOrFail();
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
