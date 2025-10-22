<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentItem>
 */
final class ShipmentItemFactory extends Factory
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
            'order_item_id' => \App\Models\OrderItem::factory(),
            'external_product_id' => $this->faker->optional()->bothify('EXT-PROD-####'),
            'product_name' => $this->faker->optional()->words(3, true),
            'product_sku' => $this->faker->optional()->bothify('SKU-####'),
            'quantity' => $this->faker->numberBetween(1, 10),
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 50),
            'length' => $this->faker->optional()->randomFloat(2, 10, 200),
            'width' => $this->faker->optional()->randomFloat(2, 10, 200),
            'height' => $this->faker->optional()->randomFloat(2, 10, 200),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
