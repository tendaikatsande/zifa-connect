<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerContract extends Model
{
    protected $fillable = [
        'player_id',
        'club_id',
        'start_date',
        'end_date',
        'contract_file_url',
        'salary_usd',
        'signing_fee_usd',
        'release_clause_usd',
        'status',
        'terms',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary_usd' => 'decimal:2',
        'signing_fee_usd' => 'decimal:2',
        'release_clause_usd' => 'decimal:2',
        'meta' => 'array',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }
}
