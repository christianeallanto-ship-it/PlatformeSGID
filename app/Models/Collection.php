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
}
