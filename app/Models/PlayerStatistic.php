<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStatistic extends Model
{
    protected $fillable = [
        'player_id',
        'season',
        'competition_id',
        'club_id',
        'matches_played',
        'matches_started',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'minutes_played',
        'clean_sheets',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
