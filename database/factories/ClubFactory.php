<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' FC',
            'short_name' => strtoupper(fake()->lexify('???')),
            'founded_year' => fake()->year(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'category' => fake()->randomElement(['premier', 'division_one', 'division_two', 'regional']),
            'status' => 'active',
            'affiliation_status' => 'active',
            'created_by' => User::factory(),
        ];
    }

    public function premier(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'premier',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
