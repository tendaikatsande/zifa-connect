<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerDocument extends Model
{
    protected $fillable = [
        'player_id',
        'type',
        'file_url',
        'file_name',
        'file_size',
        'verified',
        'verified_by',
        'verified_at',
        'expiry_date',
        'meta',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'expiry_date' => 'date',
        'meta' => 'array',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
