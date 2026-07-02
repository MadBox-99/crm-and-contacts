<?php

declare(strict_types=1);

use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->shipmentService = resolve(ShipmentService::class);
    $this->customer = Customer::factory()->create();
});

it('creates shipment from order', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

    $shipment = $this->shipmentService->createFromOrder($order, 'DPD');

    expect($shipment->customer_id)->toBe($this->customer->id)
        ->and($shipment->order_id)->toBe($order->id)
        ->and($shipment->carrier)->toBe('DPD')
        ->and($shipment->status)->toBe(ShipmentStatus::Pending)
        ->and($shipment->shipment_number)->toStartWith('SHP-');
});

it('copies order items to shipment items', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->count(4)->create(['order_id' => $order->id]);

    $shipment = $this->shipmentService->createFromOrder($order, 'GLS');

    expect($shipment->items)->toHaveCount(4);
});

it('generates shipping document PDF', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id]);

    $shipment = $this->shipmentService->createFromOrder($order, 'DPD');
    $path = $this->shipmentService->generateShippingDocument($shipment);

    expect($path)->toBeString()
        ->and(file_exists($path))->toBeTrue();

    // Cleanup
    @unlink($path);
});
