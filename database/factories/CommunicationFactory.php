<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Communication;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Communication>
 */
final class CommunicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $channel = fake()->randomElement(['email', 'sms', 'chat', 'social']);
        $sentAt = fake()->boolean(80) ? fake()->dateTimeBetween('-1 month', 'now') : null;
        $deliveredAt = $sentAt && fake()->boolean(80) ? fake()->dateTimeBetween($sentAt, 'now') : null;
        $readAt = $deliveredAt && fake()->boolean(50) ? fake()->dateTimeBetween($deliveredAt, 'now') : null;

        $status = match (true) {
            $readAt !== null => 'read',
            $deliveredAt !== null => 'delivered',
            $sentAt !== null => 'sent',
            default => fake()->randomElement(['pending', 'failed']),
        };

        return [
            'customer_id' => Customer::factory(),
            'channel' => $channel,
            'direction' => fake()->randomElement(['inbound', 'outbound']),
            'subject' => $channel === 'email' ? fake()->sentence() : null,
            'content' => fake()->paragraph(),
            'status' => $status,
            'sent_at' => $sentAt,
            'delivered_at' => $deliveredAt,
            'read_at' => $readAt,
        ];
    }
}
