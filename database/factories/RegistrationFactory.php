<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_number' => 'REG-' . date('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'entity_type' => 'player',
            'entity_id' => fake()->numberBetween(1, 1000),
            'club_id' => Club::factory(),
            'season' => date('Y'),
            'status' => 'pending_payment',
            'submitted_at' => now(),
        ];
    }
}
