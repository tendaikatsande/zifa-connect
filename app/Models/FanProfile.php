<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nickname',
        'favorite_club_id',
        'favorite_player_id',
        'city',
        'member_since',
        'loyalty_points',
        'membership_tier',
        'preferences',
    ];

    protected function casts(): array
    {
        return [
            'member_since' => 'date',
            'preferences' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'favorite_club_id');
    }

    public function favoritePlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'favorite_player_id');
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
        $this->updateMembershipTier();
    }

    protected function updateMembershipTier(): void
    {
        $tier = match (true) {
            $this->loyalty_points >= 10000 => 'platinum',
            $this->loyalty_points >= 5000 => 'gold',
            $this->loyalty_points >= 1000 => 'silver',
            default => 'bronze',
        };

        if ($this->membership_tier !== $tier) {
            $this->update(['membership_tier' => $tier]);
        }
    }
}
