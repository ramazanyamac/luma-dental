<?php

namespace Database\Factories;

use App\Models\Treatment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'treatment_id' => Treatment::factory(),
            'starts_at' => now()->addDay()->startOfDay()->addHours(9),
            'ends_at' => now()->addDay()->startOfDay()->addHours(9)->addMinutes(30),
            'name' => fake()->name(), 'email' => fake()->safeEmail(), 'phone' => '+905321234567',
            'locale' => 'tr', 'status' => 'confirmed',
        ];
    }
}
