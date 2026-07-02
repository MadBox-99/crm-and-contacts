<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use Illuminate\Support\Facades\Event;

it('dispatches OrderCreated event when an order is created', function (): void {
    Event::fake([OrderCreated::class]);

    $order = Order::factory()->create();

    Event::assertDispatched(OrderCreated::class, fn (OrderCreated $event): bool => $event->order->id === $order->id);
});

it('dispatches OrderStatusChanged event when status changes', function (): void {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    Event::fake([OrderStatusChanged::class]);

    $order->update(['status' => OrderStatus::Confirmed]);

    Event::assertDispatched(OrderStatusChanged::class, fn (OrderStatusChanged $event): bool => $event->order->id === $order->id
        && $event->previousStatus === OrderStatus::Pending->value);
});

it('does not dispatch OrderStatusChanged when other fields change', function (): void {
    $order = Order::factory()->create();

    Event::fake([OrderStatusChanged::class]);

    $order->update(['notes' => 'Updated notes']);

    Event::assertNotDispatched(OrderStatusChanged::class);
});
