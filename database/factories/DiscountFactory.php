<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Enums\DiscountValueType;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
final class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(DiscountType::class);
        $valueType = fake()->randomElement(DiscountValueType::class);
        $value = $valueType === DiscountValueType::Percentage ? fake()->randomFloat(2, 5, 50) : fake()->randomFloat(2, 10, 500);

        $validFrom = fake()->boolean(70) ? fake()->dateTimeBetween('-1 month', '+1 month') : null;
        $validUntil = $validFrom && fake()->boolean(80) ? fake()->dateTimeBetween($validFrom, '+3 months') : null;

        return [
            'name' => fake()->words(3, true),
            'type' => $type,
            'value_type' => $valueType,
            'value' => $value,
            'min_quantity' => $type === DiscountType::Quantity ? fake()->randomFloat(2, 1, 100) : null,
            'min_value' => $type === DiscountType::ValueThreshold ? fake()->randomFloat(2, 50, 1000) : null,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'customer_id' => fake()->boolean(30) ? Customer::factory() : null,
            'product_id' => fake()->boolean(40) ? Product::factory() : null,
            'is_active' => fake()->boolean(80),
            'description' => fake()->boolean(60) ? fake()->sentence() : null,
        ];
    }
}
