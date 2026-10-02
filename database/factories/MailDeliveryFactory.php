<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\MailDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MailDelivery>
 */
class MailDeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'kind' => 'confirmed',
            'recipient' => fake()->safeEmail(),
            'locale' => 'tr',
            'deduplication_key' => fake()->uuid(),
            'payload' => [],
        ];
    }
}
