<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Afficher la carte interactive de suivi.
     */
    public function index()
    {
        $bins = Bin::inActiveCity()->get();

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

        $thresholdAlmostFull = \App\Helpers\SettingsHelper::get('threshold_almost_full', 60);
        $thresholdFull = \App\Helpers\SettingsHelper::get('threshold_full', 80);

        return view('map.index', compact('bins', 'center', 'thresholdAlmostFull', 'thresholdFull', 'mapCity'));
    }
}
