<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BugReportStatus;
use App\Enums\ComplaintSeverity;
use App\Models\BugReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BugReport>
 */
final class BugReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'severity' => fake()->randomElement(ComplaintSeverity::class),
            'status' => fake()->randomElement(BugReportStatus::class),
            'source' => fake()->word(),
            'assigned_to' => fake()->boolean(70) ? User::factory() : null,
            'resolved_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
