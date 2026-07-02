<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->orderService = resolve(OrderService::class);
    $this->customer = Customer::factory()->create();
});

it('creates an order with items', function (): void {
    $items = [
        ['description' => 'Item 1', 'quantity' => 2, 'unit_price' => 100, 'tax_rate' => 27],
        ['description' => 'Item 2', 'quantity' => 1, 'unit_price' => 500, 'discount_amount' => 50, 'tax_rate' => 27],
    ];

    $order = $this->orderService->createOrder($this->customer, $items);

    expect($order->customer_id)->toBe($this->customer->id)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->orderItems)->toHaveCount(2)
        ->and($order->order_number)->toStartWith('ORD-');
});

it('generates unique order numbers', function (): void {
    $items = [['description' => 'Item', 'quantity' => 1, 'unit_price' => 100, 'tax_rate' => 0]];

    $order1 = $this->orderService->createOrder($this->customer, $items);
    $order2 = $this->orderService->createOrder($this->customer, $items);

    expect($order1->order_number)->not->toBe($order2->order_number);
});

it('allows valid status transitions', function (OrderStatus $from, OrderStatus $to): void {
    $order = Order::factory()->create(['status' => $from]);

    $updated = $this->orderService->transitionStatus($order, $to);

    expect($updated->status)->toBe($to);
})->with([
    'Pending → Confirmed' => [OrderStatus::Pending, OrderStatus::Confirmed],
    'Confirmed → Processing' => [OrderStatus::Confirmed, OrderStatus::Processing],
    'Processing → Shipped' => [OrderStatus::Processing, OrderStatus::Shipped],
    'Shipped → Delivered' => [OrderStatus::Shipped, OrderStatus::Delivered],
    'Pending → Cancelled' => [OrderStatus::Pending, OrderStatus::Cancelled],
]);

it('rejects invalid status transitions', function (): void {
    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

    $this->orderService->transitionStatus($order, OrderStatus::Processing);
})->throws(InvalidArgumentException::class);

it('creates order from accepted quote', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
        'subtotal' => 1000,
        'discount_amount' => 100,
        'tax_amount' => 243,
        'total' => 1143,
    ]);

    QuoteItem::factory()->count(3)->create(['quote_id' => $quote->id]);

    $order = $this->orderService->createFromQuote($quote);

    expect($order->customer_id)->toBe($this->customer->id)
        ->and($order->quote_id)->toBe($quote->id)
        ->and($order->orderItems)->toHaveCount(3)
        ->and($order->status)->toBe(OrderStatus::Pending);
});

it('rejects creating order from non-accepted quote', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Draft,
    ]);

    $this->orderService->createFromQuote($quote);
})->throws(InvalidArgumentException::class);
