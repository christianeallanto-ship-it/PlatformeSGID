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

        $villes = \App\Helpers\CityHelper::getCities();

        $center = $villes[$mapCity] ?? $villes['Cotonou'];
        
        // Réduction du rayon à 0.035 pour éviter le chevauchement entre Cotonou et Abomey-Calavi (environ 4 km)
        $delta = 0.035;

        return $query->whereBetween('latitude', [$center['lat'] - $delta, $center['lat'] + $delta])
                     ->whereBetween('longitude', [$center['lng'] - $delta, $center['lng'] + $delta]);
    }
}
