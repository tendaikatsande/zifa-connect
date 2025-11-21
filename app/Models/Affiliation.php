<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affiliation extends Model
{
    protected $fillable = [
        'club_id',
        'season',
        'status',
        'expiry_date',
        'payment_status',
        'approved_by',
        'approved_at',
        'meta',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->expiry_date || $this->expiry_date->isFuture());
    }
}
