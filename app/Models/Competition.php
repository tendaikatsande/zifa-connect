<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'type',
        'season',
        'region_id',
        'age_group',
        'status',
        'start_date',
        'end_date',
        'max_teams',
        'entry_fee_usd',
        'rules',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'entry_fee_usd' => 'decimal:2',
        'rules' => 'array',
        'meta' => 'array',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'competition_teams')
            ->withPivot('status', 'points', 'played', 'won', 'drawn', 'lost', 'goals_for', 'goals_against', 'goal_difference', 'position')
            ->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match::class);
    }

    public function standings()
    {
        return $this->teams()
            ->orderByPivot('points', 'desc')
            ->orderByPivot('goal_difference', 'desc')
            ->orderByPivot('goals_for', 'desc');
    }
}
