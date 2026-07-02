<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;
    protected $fillable = [
        'route_name',
        'driver_name',
        'scheduled_at',
        'status'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Scope pour filtrer les collectes selon le lieu d'intervention actif.
     */
    public function scopeInActiveCity($query)
    {
        $mapCity = \App\Helpers\SettingsHelper::get('map_city', 'Cotonou');

        if ($mapCity === 'Tous') {
            return $query;
        }

        // Par défaut, les tournées générées par les seeders (Akpakpa, Zongo, etc.) appartiennent à Cotonou
        if ($mapCity === 'Cotonou') {
            return $query->where(function ($q) {
                $q->where('route_name', 'like', '%Cotonou%')
                  ->orWhere('route_name', 'like', '%Akpakpa%')
                  ->orWhere('route_name', 'like', '%Zongo%')
                  ->orWhere('route_name', 'like', '%Gbedjromede%')
                  ->orWhere('route_name', 'like', '%Cadjehoun%')
                  ->orWhere('route_name', 'like', '%Kouhounou%')
                  ->orWhere('route_name', 'like', '%Menontin%');
            });
        }

        return $query->where('route_name', 'like', '%' . $mapCity . '%');
    }
}
