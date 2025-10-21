<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
final class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(CustomerType::class);

        return [
            'unique_identifier' => fake()->unique()->numerify('CUST-######'),
            'name' => $type === CustomerType::Company ? fake()->company() : fake()->name(),
            'type' => $type,
            'tax_number' => $type === CustomerType::Company ? fake()->numerify('########-#-##') : null,
            'registration_number' => $type === CustomerType::Company ? fake()->numerify('##-##-######') : null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->paragraph(),
            'is_active' => fake()->boolean(90),
        ];
    }

    public function b2b(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CustomerType::Company,
            'name' => fake()->company(),
            'tax_number' => fake()->numerify('########-#-##'),
            'registration_number' => fake()->numerify('##-##-######'),
        ]);
    }

    public function b2c(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CustomerType::Individual,
            'name' => fake()->name(),
            'tax_number' => null,
            'registration_number' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
