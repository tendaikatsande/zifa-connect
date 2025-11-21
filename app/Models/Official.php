<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Official extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'club_id',
        'zifa_id',
        'role',
        'license_level',
        'license_expiry',
        'license_file_url',
        'status',
        'qualifications',
        'meta',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'qualifications' => 'array',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function courseAttendance(): HasMany
    {
        return $this->hasMany(CourseAttendance::class, 'user_id', 'user_id');
    }

    public function hasValidLicense(): bool
    {
        return $this->license_expiry && $this->license_expiry->isFuture();
    }
}
