<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'other_names' => fake()->optional()->firstName(),
            'dob' => fake()->dateTimeBetween('-40 years', '-16 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['M', 'F']),
            'nationality' => 'Zimbabwean',
            'place_of_birth' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'national_id' => fake()->numerify('##-######-#-##'),
            'passport_number' => fake()->optional()->numerify('FN######'),
            'current_club_id' => Club::factory(),
            'registration_category' => fake()->randomElement(['senior', 'u20', 'u17', 'women']),
            'primary_position' => fake()->randomElement(['GK', 'CB', 'LB', 'RB', 'CDM', 'CM', 'CAM', 'LW', 'RW', 'ST']),
            'secondary_position' => fake()->optional()->randomElement(['CB', 'CM', 'ST']),
            'height_cm' => fake()->numberBetween(160, 200),
            'weight_kg' => fake()->numberBetween(55, 100),
            'dominant_foot' => fake()->randomElement(['left', 'right', 'both']),
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'zifa_id' => 'ZFA-P-' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
        ]);
    }

    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
        ]);
    }
}
