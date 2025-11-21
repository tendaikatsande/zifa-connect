<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'entity_type',
        'entity_id',
        'description',
        'category',
        'amount_cents',
        'currency',
        'status',
        'due_date',
        'paid_date',
        'issued_to_club_id',
        'issued_to_user_id',
        'created_by',
        'line_items',
        'meta',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'line_items' => 'array',
        'meta' => 'array',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'issued_to_club_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_to_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date && $this->due_date->isPast();
    }

    public function getTotalPaidAttribute(): int
    {
        return $this->payments()->where('status', 'paid')->sum('amount_cents');
    }

    public function getBalanceAttribute(): int
    {
        return $this->amount_cents - $this->total_paid;
    }
}
