<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentFactory extends Factory
{
    public function definition(): array
    {
        return ['code' => fake()->unique()->slug(2), 'duration_minutes' => 30, 'is_active' => true, 'sort_order' => 0];
    }
}
