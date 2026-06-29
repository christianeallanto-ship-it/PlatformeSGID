<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = [
        'bin_id',
        'message',
        'is_resolved'
    ];

    /**
     * Obtenir le bac associé à cette alerte.
     */
    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
