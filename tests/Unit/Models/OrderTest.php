<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $order = Order::factory()->create();

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->order_number)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quote::factory()->create(['customer_id' => $customer->id]);

    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'quote_id' => $quote->id,
        'order_number' => 'ORD-001',
        'order_date' => now(),
        'status' => OrderStatus::Pending,
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
        'notes' => 'Test notes',
    ]);

    expect($order->customer_id)->toBe($customer->id)
        ->and($order->quote_id)->toBe($quote->id)
        ->and($order->order_number)->toBe('ORD-001')
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->subtotal)->toBe('100.00')
        ->and($order->discount_amount)->toBe('10.00')
        ->and($order->tax_amount)->toBe('24.30')
        ->and($order->total)->toBe('114.30')
        ->and($order->notes)->toBe('Test notes');
});

it('casts order_date to date', function (): void {
    $order = Order::factory()->create(['order_date' => '2025-01-15']);

    expect($order->order_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts money fields to decimal', function (): void {
    $order = Order::factory()->create([
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
    ]);

    expect($order->subtotal)->toBe('100.00')
        ->and($order->discount_amount)->toBe('10.00')
        ->and($order->tax_amount)->toBe('24.30')
        ->and($order->total)->toBe('114.30');
});

it('uses soft deletes', function (): void {
    $order = Order::factory()->create();
    $order->delete();

    expect($order->trashed())->toBeTrue();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    expect($order->customer)->toBeInstanceOf(Customer::class)
        ->and($order->customer->id)->toBe($customer->id);
});

it('has quote relationship', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quote::factory()->create(['customer_id' => $customer->id]);
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'quote_id' => $quote->id,
    ]);

    expect($order->quote)->toBeInstanceOf(Quote::class)
        ->and($order->quote->id)->toBe($quote->id);
});

it('has items relationship', function (): void {
    $order = Order::factory()->create();
    $item = OrderItem::factory()->create(['order_id' => $order->id]);

    expect($order->orderItems)->toHaveCount(1)
        ->and($order->orderItems->first()->id)->toBe($item->id);
});
