<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseAttendance extends Model
{
    protected $table = 'course_attendance';

    protected $fillable = [
        'course_id',
        'user_id',
        'attended',
        'passed',
        'score',
        'certificate_url',
        'certificate_expiry',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'passed' => 'boolean',
        'score' => 'decimal:2',
        'certificate_expiry' => 'date',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
