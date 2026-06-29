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
        'type',
        'status'
    ];

    /**
     * Obtenir les alertes associées à ce bac.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
