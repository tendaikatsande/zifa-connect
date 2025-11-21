<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'registration_number',
        'entity_type',
        'entity_id',
        'season',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'notes',
        'rejection_reason',
        'meta',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending_payment', 'pending_review']);
    }
}
