<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatSession>
 */
final class ChatSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $endedAt = fake()->boolean(60) ? fake()->dateTimeBetween($startedAt, 'now') : null;

        return [
            'customer_id' => Customer::factory(),
            'user_id' => fake()->boolean(80) ? User::factory() : null,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'status' => $endedAt ? fake()->randomElement(['closed', 'transferred']) : 'active',
        ];
    }
}
