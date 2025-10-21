<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintEscalation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComplaintEscalation>
 */
final class ComplaintEscalationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'escalated_to' => User::factory(),
            'escalated_by' => User::factory(),
            'reason' => fake()->sentence(),
            'escalated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
