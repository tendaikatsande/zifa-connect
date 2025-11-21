<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_name',
        'registration_number',
        'region_id',
        'email',
        'phone',
        'address',
        'home_ground',
        'logo_url',
        'status',
        'registration_date',
        'affiliation_expiry',
        'established_year',
        'category',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'affiliation_expiry' => 'date',
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

    public function officials(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_officials')
            ->withPivot('position', 'status', 'start_date', 'end_date')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClubDocument::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'current_club_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PlayerContract::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(Match::class, 'home_club_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(Match::class, 'away_club_id');
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_teams')
            ->withPivot('status', 'points', 'played', 'won', 'drawn', 'lost', 'goals_for', 'goals_against', 'goal_difference', 'position')
            ->withTimestamps();
    }

    public function transfersIn(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_club_id');
    }

    public function transfersOut(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_club_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'issued_to_club_id');
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(FundDisbursement::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasValidAffiliation(): bool
    {
        return $this->affiliation_expiry && $this->affiliation_expiry->isFuture();
    }
}
