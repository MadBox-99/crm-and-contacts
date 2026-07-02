<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentItem>
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
            'shipment_id' => Shipment::factory(),
            'order_item_id' => OrderItem::factory(),
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
