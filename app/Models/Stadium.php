<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stadium extends Model
{
    protected $fillable = [
        'name',
        'region_id',
        'address',
        'latitude',
        'longitude',
        'capacity',
        'surface',
        'has_floodlights',
        'has_var',
        'status',
        'license_grade',
        'license_expiry',
        'facilities',
        'meta',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'has_floodlights' => 'boolean',
        'has_var' => 'boolean',
        'license_expiry' => 'date',
        'facilities' => 'array',
        'meta' => 'array',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
