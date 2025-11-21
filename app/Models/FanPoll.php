<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanPoll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'match_id',
        'competition_id',
        'status',
        'starts_at',
        'ends_at',
        'is_featured',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FanPollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FanPollVote::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->starts_at === null || $this->starts_at <= now())
            && ($this->ends_at === null || $this->ends_at >= now());
    }

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }

    public function winner(): ?FanPollOption
    {
        return $this->options()->orderByDesc('votes_count')->first();
    }
}
