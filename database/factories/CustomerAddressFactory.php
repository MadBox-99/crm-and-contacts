<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerAddress>
 */
final class CustomerAddressFactory extends Factory
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
            'type' => fake()->randomElement(['billing', 'shipping']),
            'country' => 'Hungary',
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'building_number' => fake()->buildingNumber(),
            'floor' => fake()->optional()->numberBetween(1, 10),
            'door' => fake()->optional()->numberBetween(1, 50),
            'is_default' => false,
        ];
    }

    public function billing(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'billing',
        ]);
    }

    public function shipping(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'shipping',
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_default' => true,
        ]);
    }
}
