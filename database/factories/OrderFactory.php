<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
final class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.2);
        $taxAmount = ($subtotal - $discountAmount) * 0.27;
        $total = $subtotal - $discountAmount + $taxAmount;

        return [
            'customer_id' => Customer::factory(),
            'quote_id' => null,
            'order_number' => fake()->unique()->numerify('ORD-######'),
            'order_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}
