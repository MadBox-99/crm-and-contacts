<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentTrackingEvent>
 */
final class ShipmentTrackingEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipment_id' => \App\Models\Shipment::factory(),
            'status_code' => $this->faker->randomElement([
                'PICKED_UP',
                'IN_TRANSIT',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'FAILED_DELIVERY',
                'RETURNED',
            ]),
            'location' => $this->faker->city().', '.$this->faker->country(),
            'description' => $this->faker->sentence(),
            'occurred_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'metadata' => $this->faker->optional()->randomElements([
                'temperature' => $this->faker->randomFloat(1, -20, 40),
                'handler_id' => $this->faker->randomNumber(5),
                'facility' => $this->faker->company(),
            ]),
        ];
    }
}
