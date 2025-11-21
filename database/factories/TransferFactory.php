<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transfer_reference' => 'TRF-' . date('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'player_id' => Player::factory(),
            'from_club_id' => Club::factory(),
            'to_club_id' => Club::factory(),
            'type' => fake()->randomElement(['local', 'international', 'loan', 'free']),
            'status' => 'pending_from_club',
            'transfer_fee_cents' => fake()->numberBetween(0, 10000000),
            'admin_fee_cents' => 10000,
            'initiated_at' => now(),
        ];
    }

    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'local',
            'admin_fee_cents' => 10000,
        ]);
    }

    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'international',
            'admin_fee_cents' => 50000,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
            'transfer_certificate_number' => 'TC-' . date('Ymd') . '-' . fake()->unique()->numberBetween(1000, 9999),
        ]);
    }
}
