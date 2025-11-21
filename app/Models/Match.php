<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Match extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'match_number',
        'competition_id',
        'home_club_id',
        'away_club_id',
        'venue',
        'match_date',
        'status',
        'score_home',
        'score_away',
        'ht_score_home',
        'ht_score_away',
        'referee_id',
        'assistant_referee_1_id',
        'assistant_referee_2_id',
        'fourth_official_id',
        'match_commissioner_id',
        'report_submitted',
        'report_url',
        'attendance',
        'notes',
        'meta',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'report_submitted' => 'boolean',
        'meta' => 'array',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function matchCommissioner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'match_commissioner_id');
    }

    public function squads(): HasMany
    {
        return $this->hasMany(MatchSquad::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function homeSquad(): HasMany
    {
        return $this->hasMany(MatchSquad::class)->where('club_id', $this->home_club_id);
    }

    public function awaySquad(): HasMany
    {
        return $this->hasMany(MatchSquad::class)->where('club_id', $this->away_club_id);
    }

    public function getResultAttribute(): string
    {
        if ($this->score_home === null || $this->score_away === null) {
            return 'TBD';
        }
        return "{$this->score_home} - {$this->score_away}";
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }
}
