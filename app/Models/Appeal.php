<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appeal extends Model
{
    protected $fillable = [
        'case_id',
        'grounds',
        'status',
        'submitted_by',
        'hearing_date',
        'decision',
        'decision_date',
    ];

    protected $casts = [
        'hearing_date' => 'datetime',
        'decision_date' => 'datetime',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
