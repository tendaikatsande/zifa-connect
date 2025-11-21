<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_reference',
        'player_id',
        'from_club_id',
        'to_club_id',
        'type',
        'fifa_tms_id',
        'transfer_window',
        'requested_by',
        'status',
        'transfer_fee_usd',
        'admin_fee_usd',
        'from_club_approved_by',
        'from_club_approved_at',
        'zifa_approved_by',
        'zifa_approved_at',
        'effective_date',
        'notes',
        'rejection_reason',
        'certificate_url',
        'meta',
    ];

    protected $casts = [
        'from_club_approved_at' => 'datetime',
        'zifa_approved_at' => 'datetime',
        'effective_date' => 'date',
        'transfer_fee_usd' => 'decimal:2',
        'admin_fee_usd' => 'decimal:2',
        'meta' => 'array',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function fromClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'from_club_id');
    }

    public function toClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'to_club_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function fromClubApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_club_approved_by');
    }

    public function zifaApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'zifa_approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TransferDocument::class);
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'completed']);
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['requested', 'pending_from_club', 'pending_payment', 'pending_zifa_review']);
    }
}
