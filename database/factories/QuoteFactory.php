<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
final class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-2 months', 'now');
        $validUntil = fake()->dateTimeBetween($issueDate, '+1 month');

        $subtotal = fake()->randomFloat(2, 500, 20000);
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.15);
        $taxAmount = fake()->randomFloat(2, 0, ($subtotal - $discountAmount) * 0.2);
        $total = $subtotal - $discountAmount + $taxAmount;

        return [
            'customer_id' => Customer::factory(),
            'opportunity_id' => fake()->boolean(60) ? Opportunity::factory() : null,
            'quote_number' => 'QT-'.fake()->unique()->numerify('######'),
            'issue_date' => $issueDate,
            'valid_until' => $validUntil,
            'status' => fake()->randomElement(['draft', 'sent', 'accepted', 'rejected', 'expired']),
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'notes' => fake()->boolean(40) ? fake()->sentence() : null,
        ];
    }
}
