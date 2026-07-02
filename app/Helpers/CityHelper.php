<?php

namespace App\Helpers;

use App\Models\Bin;

class CityHelper
{
    /**
     * Obtenir la liste dynamique de toutes les villes disponibles (villes par défaut + villes des bacs existants).
     */
    public static function getCities()
    {
        // 1. Liste par défaut des grandes villes du Bénin avec leurs coordonnées fixes du centre
        $defaultCities = [
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

        $cities = [
            'Tous' => ['lat' => 8.5000, 'lng' => 2.3000]
        ];

        // Ajouter les villes par défaut
        foreach ($defaultCities as $name => $coords) {
            $cities[$name] = $coords;
        }

        // 2. Parcourir tous les bacs en base de données pour détecter les nouvelles villes
        $bins = Bin::all();
        $binsByCity = [];

        foreach ($bins as $bin) {
            if ($bin->location) {
                // On extrait le nom de la ville après la virgule (ex: "Akpakpa, Cotonou" -> "Cotonou")
                $parts = explode(',', $bin->location);
                $cityName = count($parts) > 1 ? trim(end($parts)) : trim($bin->location);

                if ($cityName) {
                    // Normalisation de la casse (ex: "savalou" -> "Savalou")
                    $cityName = ucfirst(strtolower($cityName));
                    $binsByCity[$cityName][] = $bin;
                }
            }
        }

        // 3. Ajouter les nouvelles villes en calculant leur centre géographique moyen
        foreach ($binsByCity as $name => $cityBins) {
            if (!isset($cities[$name])) {
                $latSum = 0;
                $lngSum = 0;
                $count = 0;
                foreach ($cityBins as $b) {
                    if ($b->latitude && $b->longitude) {
                        $latSum += $b->latitude;
                        $lngSum += $b->longitude;
                        $count++;
                    }
                }
                if ($count > 0) {
                    $cities[$name] = [
                        'lat' => $latSum / $count,
                        'lng' => $lngSum / $count
                    ];
                } else {
                    // Centre par défaut si pas de coordonnées
                    $cities[$name] = ['lat' => 6.3703, 'lng' => 2.4308];
                }
            }
        }

        return $cities;
    }
}
