<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignResponse;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampaignResponse>
 */
final class CampaignResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'customer_id' => Customer::factory(),
            'response_type' => fake()->randomElement(['interested', 'not_interested', 'callback', 'no_response']),
            'notes' => fake()->boolean(50) ? fake()->sentence() : null,
            'responded_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }
}
