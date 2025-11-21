<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinarySanction extends Model
{
    protected $fillable = [
        'case_id',
        'sanction_type',
        'description',
        'fine_amount_usd',
        'suspension_matches',
        'suspension_start',
        'suspension_end',
        'points_deducted',
        'status',
        'invoice_id',
    ];

    protected $casts = [
        'fine_amount_usd' => 'decimal:2',
        'suspension_start' => 'date',
        'suspension_end' => 'date',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
