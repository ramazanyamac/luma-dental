<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityWindowFactory extends Factory
{
    public function definition(): array
    {
        return ['starts_at' => now()->addDay()->startOfDay()->addHours(9), 'ends_at' => now()->addDay()->startOfDay()->addHours(17)];
    }
}
