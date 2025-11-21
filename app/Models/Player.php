<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'zifa_id',
        'fifa_connect_id',
        'first_name',
        'last_name',
        'other_names',
        'dob',
        'place_of_birth',
        'gender',
        'nationality',
        'height_cm',
        'weight_kg',
        'dominant_foot',
        'marital_status',
        'address',
        'phone',
        'email',
        'photo_url',
        'current_club_id',
        'status',
        'registration_category',
        'primary_position',
        'secondary_position',
        'national_id',
        'passport_number',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'dob' => 'date',
        'meta' => 'array',
    ];

    protected $appends = ['full_name', 'age'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PlayerDocument::class);
    }

    public function medicals(): HasMany
    {
        return $this->hasMany(PlayerMedical::class);
    }

    public function latestMedical(): HasOne
    {
        return $this->hasOne(PlayerMedical::class)->latestOfMany();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PlayerContract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(PlayerContract::class)->where('status', 'active');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(PlayerStatistic::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function transferHistory(): HasMany
    {
        return $this->hasMany(TransferHistory::class);
    }

    public function matchSquads(): HasMany
    {
        return $this->hasMany(MatchSquad::class);
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'entity_id')
            ->where('entity_type', 'player');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(AgentPlayer::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob?->age;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isFreeAgent(): bool
    {
        return $this->status === 'free_agent' || $this->current_club_id === null;
    }

    public function isEligibleForTransfer(): bool
    {
        return in_array($this->status, ['approved', 'free_agent']);
    }
}
