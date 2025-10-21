<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteItem>
 */
final class QuoteItemFactory extends Factory
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
        $discountPercent = fake()->randomFloat(2, 0, 20);
        $discountAmount = ($quantity * $unitPrice) * ($discountPercent / 100);
        $taxRate = fake()->randomFloat(2, 0, 25);
        $subtotal = $quantity * $unitPrice - $discountAmount;
        $total = $subtotal + ($subtotal * $taxRate / 100);

        return [
            'quote_id' => Quote::factory(),
            'product_id' => fake()->boolean(90) ? Product::factory() : null,
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
