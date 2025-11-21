<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'zifa_id',
        'category',
        'region_id',
        'fitness_test_expiry',
        'license_expiry',
        'license_file_url',
        'status',
        'matches_officiated',
        'average_rating',
        'meta',
    ];

    protected $casts = [
        'fitness_test_expiry' => 'date',
        'license_expiry' => 'date',
        'average_rating' => 'decimal:2',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match::class);
    }

    public function hasValidLicense(): bool
    {
        return $this->license_expiry && $this->license_expiry->isFuture();
    }

    public function hasFitnessTest(): bool
    {
        return $this->fitness_test_expiry && $this->fitness_test_expiry->isFuture();
    }
}
