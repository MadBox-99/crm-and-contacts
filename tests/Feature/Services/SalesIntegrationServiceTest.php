<?php

declare(strict_types=1);

use App\Contracts\SalesIntegrationInterface;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Team;
use App\Services\SalesIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(SalesIntegrationService::class);
    $this->team = Team::factory()->create();
    $this->customer = Customer::factory()->for($this->team)->create();
});

it('implements SalesIntegrationInterface', function (): void {
    expect($this->service)->toBeInstanceOf(SalesIntegrationInterface::class);
});

it('pushes order to accounting (no URL configured returns success)', function (): void {
    $order = Order::factory()->for($this->customer)->for($this->team)->create();

    $result = $this->service->pushOrderToAccounting($order);

    expect($result['success'])->toBeTrue()
        ->and($result)->toHaveKeys(['success', 'reference_id', 'message']);
});

it('pushes invoice to finance (no URL configured returns success)', function (): void {
    $order = Order::factory()->for($this->customer)->for($this->team)->create();
    $invoice = Invoice::factory()->for($this->customer)->for($this->team)->for($order)->create();

    $result = $this->service->pushInvoiceToFinance($invoice);

    expect($result['success'])->toBeTrue()
        ->and($result)->toHaveKeys(['success', 'reference_id', 'message']);
});

it('checks inventory (no URL configured returns available)', function (): void {
    $product = Product::factory()->for($this->team)->create();

    $result = $this->service->checkInventory($product, 5);

    expect($result['available'])->toBeTrue()
        ->and($result['quantity'])->toBe(5)
        ->and($result)->toHaveKeys(['available', 'quantity', 'warehouse']);
});

it('reserves stock (no URL configured returns success)', function (): void {
    $order = Order::factory()->for($this->customer)->for($this->team)->create();

    $result = $this->service->reserveStock($order, [
        ['product_id' => 1, 'quantity' => 10],
    ]);

    expect($result['success'])->toBeTrue()
        ->and($result)->toHaveKeys(['success', 'reservation_id', 'message']);
});

it('resolves from container via interface', function (): void {
    $resolved = resolve(SalesIntegrationInterface::class);

    expect($resolved)->toBeInstanceOf(SalesIntegrationService::class);
});
