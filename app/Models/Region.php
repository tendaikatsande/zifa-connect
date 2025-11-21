<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function referees(): HasMany
    {
        return $this->hasMany(Referee::class);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }
}
