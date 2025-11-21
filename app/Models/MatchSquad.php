<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSquad extends Model
{
    protected $fillable = [
        'match_id',
        'club_id',
        'player_id',
        'is_starting',
        'shirt_number',
        'position',
        'is_captain',
    ];

    protected $casts = [
        'is_starting' => 'boolean',
        'is_captain' => 'boolean',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
