<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'provider',
        'type',
        'level',
        'start_date',
        'end_date',
        'venue',
        'capacity',
        'fee_usd',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fee_usd' => 'decimal:2',
        'meta' => 'array',
    ];

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_attendance')
            ->withPivot('attended', 'passed', 'score', 'certificate_url', 'certificate_expiry')
            ->withTimestamps();
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(CourseAttendance::class, 'course_id');
    }
}
