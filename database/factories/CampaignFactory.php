<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
final class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');
        $endDate = fake()->boolean(80) ? fake()->dateTimeBetween($startDate, '+3 months') : null;

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(CampaignStatus::cases())->value,
            'target_audience_criteria' => fake()->boolean(60) ? [
                'age_range' => [fake()->numberBetween(18, 40), fake()->numberBetween(41, 65)],
                'location' => fake()->city(),
                'interests' => fake()->words(3),
            ] : null,
            'created_by' => User::factory(),
        ];
    }
}
