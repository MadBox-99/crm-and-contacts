<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
final class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = fake()->dateTimeBetween($issueDate, '+1 month');
        $status = fake()->randomElement(InvoiceStatus::class);

        $subtotal = fake()->randomFloat(2, 100, 10000);
        $discountAmount = fake()->randomFloat(2, 0, $subtotal * 0.2);
        $taxAmount = fake()->randomFloat(2, 0, ($subtotal - $discountAmount) * 0.2);
        $total = $subtotal - $discountAmount + $taxAmount;

        return [
            'customer_id' => Customer::factory(),
            'order_id' => fake()->boolean(70) ? Order::factory() : null,
            'invoice_number' => 'INV-'.fake()->unique()->numerify('######'),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'status' => $status,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'notes' => fake()->boolean(40) ? fake()->sentence() : null,
            'paid_at' => $status === InvoiceStatus::Paid ? fake()->dateTimeBetween($issueDate, 'now') : null,
        ];
    }
}
