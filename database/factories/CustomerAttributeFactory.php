<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerAttribute>
 */
final class CustomerAttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keys = ['industry', 'employee_count', 'annual_revenue', 'source', 'rating'];

        return [
            'customer_id' => Customer::factory(),
            'attribute_key' => fake()->randomElement($keys),
            'attribute_value' => fake()->word(),
        ];
    }
}
