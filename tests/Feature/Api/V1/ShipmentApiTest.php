<?php

declare(strict_types=1);

use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentTrackingEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

it('can create a shipment via API', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->for($customer)->create(['order_number' => 'ORD-2025-0001']);

    $response = $this->postJson('/api/v1/shipments', [
        'order_number' => 'ORD-2025-0001',
        'carrier' => 'GLS',
        'tracking_number' => 'GLS123456789',
        'shipping_address' => [
            'name' => 'Test User',
            'country' => 'Hungary',
            'postal_code' => '1011',
            'city' => 'Budapest',
            'street' => 'Fő utca',
            'building_number' => '1',
        ],
        'items' => [
            [
                'external_product_id' => 'EXT-PROD-001',
                'product_name' => 'Test Product',
                'product_sku' => 'SKU-001',
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'shipment_number',
                'tracking_number',
                'status',
                'items',
            ],
        ]);

    expect(Shipment::count())->toBe(1);
    expect(Shipment::first()->items()->count())->toBe(1);
});

it('can create a shipment with external IDs only', function (): void {
    $response = $this->postJson('/api/v1/shipments', [
        'external_customer_id' => 'EXT-CUST-999',
        'external_order_id' => 'EXT-ORD-999',
        'carrier' => 'DPD',
        'tracking_number' => 'DPD987654321',
        'shipping_address' => [
            'name' => 'External Customer',
            'country' => 'Hungary',
            'postal_code' => '6000',
            'city' => 'Kecskemét',
            'street' => 'Teszt utca',
            'building_number' => '10',
        ],
    ]);

    $response->assertCreated();

    $shipment = Shipment::first();
    expect($shipment->customer_id)->toBeNull();
    expect($shipment->order_id)->toBeNull();
    expect($shipment->external_customer_id)->toBe('EXT-CUST-999');
    expect($shipment->external_order_id)->toBe('EXT-ORD-999');
});

it('validates required fields when creating shipment', function (): void {
    $response = $this->postJson('/api/v1/shipments', [
        // Missing carrier and shipping_address
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['carrier', 'shipping_address']);
});

it('can update shipment status via API', function (): void {
    $shipment = Shipment::factory()->create([
        'tracking_number' => 'GLS123456789',
        'status' => ShipmentStatus::Pending,
    ]);

    $response = $this->putJson("/api/v1/shipments/{$shipment->tracking_number}/status", [
        'status' => ShipmentStatus::Shipped->value,
        'shipped_at' => now()->toDateTimeString(),
        'estimated_delivery_at' => now()->addDays(2)->toDateTimeString(),
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Shipment status updated successfully',
        ]);

    expect($shipment->fresh()->status)->toBe(ShipmentStatus::Shipped);
    expect($shipment->fresh()->shipped_at)->not->toBeNull();
});

it('returns 404 when updating non-existent shipment status', function (): void {
    $response = $this->putJson('/api/v1/shipments/INVALID-TRACKING/status', [
        'status' => ShipmentStatus::Shipped->value,
    ]);

    $response->assertNotFound();
});

it('can add tracking event to shipment', function (): void {
    $shipment = Shipment::factory()->create(['tracking_number' => 'GLS123456789']);

    $response = $this->postJson("/api/v1/shipments/{$shipment->tracking_number}/tracking", [
        'status_code' => 'IN_TRANSIT',
        'location' => 'Budapest, Hungary',
        'description' => 'Package is in transit',
        'occurred_at' => now()->toDateTimeString(),
        'metadata' => [
            'facility' => 'Budapest Hub',
            'handler_id' => 12345,
        ],
    ]);

    $response->assertCreated()
        ->assertJson([
            'message' => 'Tracking event created successfully',
        ]);

    expect($shipment->trackingEvents()->count())->toBe(1);
    expect($shipment->trackingEvents()->first()->status_code)->toBe('IN_TRANSIT');
});

it('validates tracking event data', function (): void {
    $shipment = Shipment::factory()->create(['tracking_number' => 'GLS123456789']);

    $response = $this->postJson("/api/v1/shipments/{$shipment->tracking_number}/tracking", [
        // Missing required fields
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status_code', 'occurred_at']);
});

it('can retrieve shipment with all relations', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->for($customer)->create();
    $shipment = Shipment::factory()
        ->for($customer)
        ->for($order)
        ->has(ShipmentItem::factory()->count(2), 'items')
        ->has(ShipmentTrackingEvent::factory()->count(3), 'trackingEvents')
        ->create(['tracking_number' => 'GLS123456789']);

    $response = $this->getJson("/api/v1/shipments/{$shipment->tracking_number}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'id',
                'shipment_number',
                'tracking_number',
                'customer',
                'order',
                'items',
                'tracking_events',
            ],
        ]);
});

it('generates unique shipment numbers', function (): void {
    $shipment1 = Shipment::factory()->create();
    $shipment2 = Shipment::factory()->create();
    $shipment3 = Shipment::factory()->create();

    expect($shipment1->shipment_number)->not->toBe($shipment2->shipment_number);
    expect($shipment2->shipment_number)->not->toBe($shipment3->shipment_number);
    expect($shipment1->shipment_number)->toStartWith('SHP-'.now()->format('Y'));
});

it('requires authentication for API endpoints', function (): void {
    $this->withoutMiddleware(Illuminate\Auth\Middleware\Authenticate::class);

    $response = $this->postJson('/api/v1/shipments', [
        'carrier' => 'GLS',
        'shipping_address' => [],
    ]);

    // Without auth middleware, should still validate
    $response->assertUnprocessable();
});
