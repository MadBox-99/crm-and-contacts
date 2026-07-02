<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Listeners\AwardLoyaltyPointsOnDelivery;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Team;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->customer = Customer::factory()->create([
        'team_id' => $this->team->id,
        'loyalty_points' => 0,
    ]);
});

it('awards points when order status changes to delivered', function (): void {
    $order = Order::factory()->create([
        'team_id' => $this->team->id,
        'customer_id' => $this->customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 5000.00,
    ]);

    $event = new OrderStatusChanged($order, OrderStatus::Shipped->value);
    $listener = resolve(AwardLoyaltyPointsOnDelivery::class);
    $listener->handle($event);

    $this->customer->refresh();

    expect($this->customer->loyalty_points)->toBe(5)
        ->and(LoyaltyPoint::query()->where('customer_id', $this->customer->id)->count())->toBe(1);
});

it('does not award points for non-delivered status changes', function (): void {
    $order = Order::factory()->create([
        'team_id' => $this->team->id,
        'customer_id' => $this->customer->id,
        'status' => OrderStatus::Shipped,
        'total' => 5000.00,
    ]);

    $event = new OrderStatusChanged($order, OrderStatus::Processing->value);
    $listener = resolve(AwardLoyaltyPointsOnDelivery::class);
    $listener->handle($event);

    $this->customer->refresh();

    expect($this->customer->loyalty_points)->toBe(0)
        ->and(LoyaltyPoint::query()->where('customer_id', $this->customer->id)->count())->toBe(0);
});

it('does not award points when order total results in zero points', function (): void {
    $order = Order::factory()->create([
        'team_id' => $this->team->id,
        'customer_id' => $this->customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 500.00,
    ]);

    $event = new OrderStatusChanged($order, OrderStatus::Shipped->value);
    $listener = resolve(AwardLoyaltyPointsOnDelivery::class);
    $listener->handle($event);

    $this->customer->refresh();

    expect($this->customer->loyalty_points)->toBe(0);
});
