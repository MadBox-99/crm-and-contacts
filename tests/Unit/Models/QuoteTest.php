<?php

declare(strict_types=1);

use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Order;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $quote = Quote::factory()->create();

    expect($quote)->toBeInstanceOf(Quote::class)
        ->and($quote->quote_number)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $opportunity = Opportunity::factory()->create(['customer_id' => $customer->id]);

    $quote = Quote::factory()->create([
        'customer_id' => $customer->id,
        'opportunity_id' => $opportunity->id,
        'quote_number' => 'QT-001',
        'issue_date' => now(),
        'valid_until' => now()->addDays(30),
        'status' => QuoteStatus::Draft,
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
        'notes' => 'Test notes',
    ]);

    expect($quote->customer_id)->toBe($customer->id)
        ->and($quote->opportunity_id)->toBe($opportunity->id)
        ->and($quote->quote_number)->toBe('QT-001')
        ->and($quote->status)->toBe(QuoteStatus::Draft)
        ->and($quote->subtotal)->toBe('100.00')
        ->and($quote->discount_amount)->toBe('10.00')
        ->and($quote->tax_amount)->toBe('24.30')
        ->and($quote->total)->toBe('114.30')
        ->and($quote->notes)->toBe('Test notes');
});

it('casts date fields to date', function (): void {
    $quote = Quote::factory()->create([
        'issue_date' => '2025-01-15',
        'valid_until' => '2025-02-15',
    ]);

    expect($quote->issue_date)->toBeInstanceOf(DateTimeInterface::class)
        ->and($quote->valid_until)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts money fields to decimal', function (): void {
    $quote = Quote::factory()->create([
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
    ]);

    expect($quote->subtotal)->toBe('100.00')
        ->and($quote->discount_amount)->toBe('10.00')
        ->and($quote->tax_amount)->toBe('24.30')
        ->and($quote->total)->toBe('114.30');
});

it('uses soft deletes', function (): void {
    $quote = Quote::factory()->create();
    $quote->delete();

    expect($quote->trashed())->toBeTrue();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quote::factory()->create(['customer_id' => $customer->id]);

    expect($quote->customer)->toBeInstanceOf(Customer::class)
        ->and($quote->customer->id)->toBe($customer->id);
});

it('has opportunity relationship', function (): void {
    $customer = Customer::factory()->create();
    $opportunity = Opportunity::factory()->create(['customer_id' => $customer->id]);
    $quote = Quote::factory()->create([
        'customer_id' => $customer->id,
        'opportunity_id' => $opportunity->id,
    ]);

    expect($quote->opportunity)->toBeInstanceOf(Opportunity::class)
        ->and($quote->opportunity->id)->toBe($opportunity->id);
});

it('has items relationship', function (): void {
    $quote = Quote::factory()->create();
    $item = QuoteItem::factory()->create(['quote_id' => $quote->id]);

    expect($quote->items)->toHaveCount(1)
        ->and($quote->items->first()->id)->toBe($item->id);
});

it('has orders relationship', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quote::factory()->create(['customer_id' => $customer->id]);
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'quote_id' => $quote->id,
    ]);

    expect($quote->orders)->toHaveCount(1)
        ->and($quote->orders->first()->id)->toBe($order->id);
});
