<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundDisbursement extends Model
{
    protected $fillable = [
        'fund_id',
        'club_id',
        'region_id',
        'amount_cents',
        'purpose',
        'description',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'disbursed_at',
        'acquittal_doc_url',
        'acquitted_at',
        'meta',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'acquitted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
