<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OpportunityStage;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opportunity>
 */
final class OpportunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stage = fake()->randomElement(OpportunityStage::cases());
        $probability = match ($stage) {
            OpportunityStage::Lead => fake()->numberBetween(10, 25),
            OpportunityStage::Qualified => fake()->numberBetween(25, 50),
            OpportunityStage::Proposal => fake()->numberBetween(50, 75),
            OpportunityStage::Negotiation => fake()->numberBetween(75, 90),
            OpportunityStage::SendedQuotation => 100,
            OpportunityStage::LostQuotation => 0,
        };

        return [
            'customer_id' => Customer::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'value' => fake()->boolean(80) ? fake()->randomFloat(2, 1000, 100000) : null,
            'probability' => $probability,
            'stage' => $stage,
            'expected_close_date' => $stage->isActive() ? fake()->dateTimeBetween('now', '+6 months') : null,
            'assigned_to' => fake()->boolean(80) ? User::factory() : null,
        ];
    }
}
