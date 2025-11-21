<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Academy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'registration_number',
        'region_id',
        'affiliated_club_id',
        'email',
        'phone',
        'address',
        'logo_url',
        'status',
        'registration_date',
        'license_expiry',
        'age_groups',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'license_expiry' => 'date',
        'age_groups' => 'array',
        'meta' => 'array',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function affiliatedClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'affiliated_club_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
