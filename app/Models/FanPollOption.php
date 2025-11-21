<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanPollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'fan_poll_id',
        'player_id',
        'club_id',
        'custom_option',
        'image_url',
        'votes_count',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(FanPoll::class, 'fan_poll_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FanPollVote::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->player) {
            return $this->player->first_name . ' ' . $this->player->last_name;
        }
        if ($this->club) {
            return $this->club->name;
        }
        return $this->custom_option ?? 'Unknown';
    }

    public function votePercentage(): float
    {
        $totalVotes = $this->poll->totalVotes();
        if ($totalVotes === 0) {
            return 0;
        }
        return round(($this->votes_count / $totalVotes) * 100, 1);
    }
}
