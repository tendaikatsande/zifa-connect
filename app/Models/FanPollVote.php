<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanPollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'fan_poll_id',
        'fan_poll_option_id',
        'user_id',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(FanPoll::class, 'fan_poll_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(FanPollOption::class, 'fan_poll_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (FanPollVote $vote) {
            $vote->option->increment('votes_count');
        });

        static::deleted(function (FanPollVote $vote) {
            $vote->option->decrement('votes_count');
        });
    }
}
