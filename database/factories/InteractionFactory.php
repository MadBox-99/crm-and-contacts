<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InteractionType;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
final class InteractionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(InteractionType::class);
        $interactionDate = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'subject' => fake()->sentence(),
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'interaction_date' => $interactionDate,
            'duration' => in_array($type, ['call', 'meeting']) ? fake()->numberBetween(5, 120) : null,
            'next_action' => fake()->boolean(40) ? fake()->sentence() : null,
            'next_action_date' => fake()->boolean(40) ? fake()->dateTimeBetween($interactionDate, '+1 month') : null,
        ];
    }
}
