<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
final class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 50);
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        $discountAmount = fake()->randomFloat(2, 0, $unitPrice * $quantity * 0.2);
        $taxRate = fake()->randomFloat(2, 0, 25);
        $subtotal = $quantity * $unitPrice - $discountAmount;
        $total = $subtotal + ($subtotal * $taxRate / 100);

        return [
            'order_id' => Order::factory(),
            'product_id' => fake()->boolean(90) ? Product::factory() : null,
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
