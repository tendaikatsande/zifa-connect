<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fund extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'total_amount_cents',
        'currency',
        'status',
        'created_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(FundDisbursement::class);
    }

    public function getBalanceAttribute(): int
    {
        $disbursed = $this->disbursements()->where('status', 'disbursed')->sum('amount_cents');
        return $this->total_amount_cents - $disbursed;
    }
}
