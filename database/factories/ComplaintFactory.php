<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
final class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reportedAt = fake()->dateTimeBetween('-3 months', 'now');
        $status = fake()->randomElement(ComplaintStatus::class);
        $resolvedAt = in_array($status, [ComplaintStatus::Resolved, ComplaintStatus::Closed]) ? fake()->dateTimeBetween($reportedAt, 'now') : null;

        return [
            'customer_id' => Customer::factory(),
            'order_id' => fake()->boolean(60) ? Order::factory() : null,
            'reported_by' => User::factory(),
            'assigned_to' => fake()->boolean(80) ? User::factory() : null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(2, true),
            'severity' => fake()->randomElement(ComplaintSeverity::class),
            'status' => $status,
            'resolution' => $resolvedAt ? fake()->paragraph() : null,
            'reported_at' => $reportedAt,
            'resolved_at' => $resolvedAt,
        ];
    }
}
