<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
final class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderType = fake()->randomElement(['customer', 'user', 'bot']);
        $senderId = match ($senderType) {
            'customer' => Customer::factory(),
            'user' => User::factory(),
            'bot' => null,
        };

        return [
            'chat_session_id' => ChatSession::factory(),
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => fake()->sentence(),
        ];
    }
}
