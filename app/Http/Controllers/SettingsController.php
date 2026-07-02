<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\Alert;

class SettingsController extends Controller
{
    /**
     * Afficher les paramètres de l'application.
     */
    public function index()
    {
        // Lire les paramètres sauvegardés de façon persistante (ou valeurs par défaut)
        $settings = [
            'threshold_almost_full' => \App\Helpers\SettingsHelper::get('threshold_almost_full', 60),
            'threshold_full'        => \App\Helpers\SettingsHelper::get('threshold_full', 80),
            'gps_frequency'         => \App\Helpers\SettingsHelper::get('gps_frequency', 15),
            'map_city'              => \App\Helpers\SettingsHelper::get('map_city', 'Cotonou'),
        ];

        $villes = [
            'Tous'          => ['lat' => '8.5000',  'lng' => '2.3000'],
            'Cotonou'       => ['lat' => '6.3703',  'lng' => '2.4308'],
            'Porto-Novo'    => ['lat' => '6.4969',  'lng' => '2.6283'],
            'Parakou'       => ['lat' => '9.3370',  'lng' => '2.6277'],
            'Abomey-Calavi' => ['lat' => '6.4490',  'lng' => '2.3554'],
            'Bohicon'       => ['lat' => '7.1781',  'lng' => '2.0717'],
            'Natitingou'    => ['lat' => '10.3103', 'lng' => '1.3786'],
            'Ouidah'        => ['lat' => '6.3612',  'lng' => '2.0854'],
            'Lokossa'       => ['lat' => '6.6384',  'lng' => '1.7173'],
            'Djougou'       => ['lat' => '9.7097',  'lng' => '1.6660'],
            'Kandi'         => ['lat' => '11.1344', 'lng' => '2.9389'],
        ];

        return view('settings.index', compact('settings', 'villes'));
    }

    /**
     * Sauvegarder les seuils et la zone d'intervention.
     */
    public function save(Request $request)
    {
        $request->validate([
            'threshold_almost_full' => 'required|integer|min:40|max:79',
            'threshold_full'        => 'required|integer|min:80|max:100',
            'gps_frequency'         => 'required|integer|in:5,15,30,60',
            'map_city'              => 'required|string|in:Tous,Cotonou,Porto-Novo,Parakou,Abomey-Calavi,Bohicon,Natitingou,Ouidah,Lokossa,Djougou,Kandi',
        ]);

        $thresholdAlmostFull = (int) $request->threshold_almost_full;
        $thresholdFull = (int) $request->threshold_full;
        $mapCity = $request->map_city;

        // Stocker les paramètres de façon persistante (fichier settings.json)
        \App\Helpers\SettingsHelper::set('threshold_almost_full', $thresholdAlmostFull);
        \App\Helpers\SettingsHelper::set('threshold_full', $thresholdFull);
        \App\Helpers\SettingsHelper::set('gps_frequency', (int) $request->gps_frequency);
        \App\Helpers\SettingsHelper::set('map_city', $mapCity);

        // Mettre à jour les statuts des bacs selon les nouveaux seuils
        $bins = Bin::all();
        foreach ($bins as $bin) {

            // Recalculer le statut en fonction des nouveaux seuils
            if ($bin->fill_level >= $thresholdFull) {
                $bin->status = 'Plein';
            } elseif ($bin->fill_level >= $thresholdAlmostFull) {
                $bin->status = 'Presque plein';
            } else {
                $bin->status = 'Normal';
            }
            $bin->save();

            // Mettre à jour ou créer les alertes correspondantes
            if ($bin->fill_level >= $thresholdAlmostFull) {
                $statusText = ($bin->fill_level >= $thresholdFull) ? 'plein' : 'presque plein';
                $alert = Alert::where('bin_id', $bin->id)->where('is_resolved', false)->first();
                if (!$alert) {
                    Alert::create([
                        'bin_id' => $bin->id,
                        'message' => "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusText} ({$bin->fill_level}%).",
                        'is_resolved' => false,
                    ]);
                } else {
                    $alert->message = "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusText} ({$bin->fill_level}%).";
                    $alert->save();
                }
            } else {
                // Si le niveau est repassé sous le seuil d'alerte, on résout l'alerte
                Alert::where('bin_id', $bin->id)->where('is_resolved', false)->update(['is_resolved' => true]);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres enregistrés avec succès. Le filtre d\'affichage a été configuré sur ' . $mapCity . ' et les statuts des bacs ont été recalculés selon les nouveaux seuils.');
    }
}
