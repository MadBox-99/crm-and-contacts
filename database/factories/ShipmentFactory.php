<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
final class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'external_customer_id' => $this->faker->optional()->bothify('EXT-CUST-####'),
            'external_order_id' => $this->faker->optional()->bothify('EXT-ORD-####'),
            'shipment_number' => 'SHP-'.now()->format('Y').'-'.mb_str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'carrier' => $this->faker->randomElement(['GLS', 'DPD', 'FoxPost', 'Magyar Posta', 'UPS', 'DHL']),
            'tracking_number' => $this->faker->optional()->bothify('??########'),
            'status' => $this->faker->randomElement(ShipmentStatus::cases()),
            'shipping_address' => [
                'name' => $this->faker->name(),
                'country' => $this->faker->country(),
                'postal_code' => $this->faker->postcode(),
                'city' => $this->faker->city(),
                'street' => $this->faker->streetName(),
                'building_number' => $this->faker->buildingNumber(),
            ],
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'estimated_delivery_at' => $this->faker->optional()->dateTimeBetween('now', '+7 days'),
            'delivered_at' => null,
            'notes' => $this->faker->optional()->sentence(),
            'documents' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShipmentStatus::Pending,
            'shipped_at' => null,
            'estimated_delivery_at' => null,
            'delivered_at' => null,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShipmentStatus::Delivered,
            'shipped_at' => now()->subDays(5),
            'estimated_delivery_at' => now()->subDays(2),
            'delivered_at' => now()->subDay(),
        ]);
    }
}
