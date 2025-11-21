<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'status',
        'ticket_reference',
        'seat_section',
        'loyalty_points_earned',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function checkIn(): void
    {
        $this->update(['status' => 'checked_in']);
    }

    public function markAttended(int $loyaltyPoints = 10): void
    {
        $this->update([
            'status' => 'attended',
            'loyalty_points_earned' => $loyaltyPoints,
        ]);

        // Award loyalty points to fan profile
        $fanProfile = FanProfile::where('user_id', $this->user_id)->first();
        if ($fanProfile) {
            $fanProfile->addLoyaltyPoints($loyaltyPoints);
        }
    }
}
