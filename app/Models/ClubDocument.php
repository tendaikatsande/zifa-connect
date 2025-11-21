<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubDocument extends Model
{
    protected $fillable = [
        'club_id',
        'type',
        'file_url',
        'file_name',
        'file_size',
        'verified_by',
        'verified_at',
        'expiry_date',
        'meta',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expiry_date' => 'date',
        'meta' => 'array',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
