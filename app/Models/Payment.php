<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_reference',
        'amount_cents',
        'currency',
        'status',
        'gateway',
        'gateway_method',
        'gateway_reference',
        'gateway_transaction_id',
        'initiated_by',
        'initiated_at',
        'paid_at',
        'gateway_response',
        'callback_payload',
        'reconciled_at',
        'notes',
        'meta',
    ];

    protected $casts = [
        'initiated_at' => 'datetime',
        'paid_at' => 'datetime',
        'reconciled_at' => 'datetime',
        'gateway_response' => 'array',
        'callback_payload' => 'array',
        'meta' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['initiated', 'pending', 'processing']);
    }
}
