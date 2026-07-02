<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'location',
        'latitude',
        'longitude',
        'fill_level',
        'temperature',
        'air_quality',
        'type',
        'status',
        'is_active'
    ];

    /**
     * Obtenir les alertes associées à ce bac.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Scope pour filtrer les bacs situés dans le lieu d'intervention actif.
     */
    public function scopeInActiveCity($query)
    {
        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');

        if ($mapCity === 'Tous') {
            return $query;
        }

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
        
        // 10 km de rayon correspond à environ 0.09 degré de latitude et de longitude au Bénin.
        $delta = 0.09;

        return $query->whereBetween('latitude', [$center['lat'] - $delta, $center['lat'] + $delta])
                     ->whereBetween('longitude', [$center['lng'] - $delta, $center['lng'] + $delta]);
    }
}
