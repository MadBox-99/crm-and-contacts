<?php

declare(strict_types=1);

use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Services\DocumentChainService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(DocumentChainService::class);
    $this->customer = Customer::factory()->create();
});

it('creates order from accepted quote with items', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
        'subtotal' => 1000,
        'tax_amount' => 270,
        'total' => 1270,
    ]);

    QuoteItem::factory()->count(2)->create(['quote_id' => $quote->id]);

    $order = $this->service->createOrderFromQuote($quote);

    expect($order->customer_id)->toBe($this->customer->id)
        ->and($order->quote_id)->toBe($quote->id)
        ->and($order->orderItems)->toHaveCount(2);
});

it('rejects creating order from non-accepted quote', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Draft,
    ]);

    $this->service->createOrderFromQuote($quote);
})->throws(InvalidArgumentException::class);

it('creates invoice from order with items', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
    ]);

    QuoteItem::factory()->count(2)->create(['quote_id' => $quote->id]);

    $order = $this->service->createOrderFromQuote($quote);
    $invoice = $this->service->createInvoiceFromOrder($order);

    expect($invoice->customer_id)->toBe($this->customer->id)
        ->and($invoice->order_id)->toBe($order->id)
        ->and($invoice->invoiceItems)->toHaveCount(2)
        ->and($invoice->invoice_number)->toStartWith('INV-');
});

it('creates shipment from order', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
    ]);

    QuoteItem::factory()->count(2)->create(['quote_id' => $quote->id]);

    $order = $this->service->createOrderFromQuote($quote);
    $shipment = $this->service->createShipmentFromOrder($order, 'DPD');

    expect($shipment->customer_id)->toBe($this->customer->id)
        ->and($shipment->order_id)->toBe($order->id)
        ->and($shipment->carrier)->toBe('DPD')
        ->and($shipment->items)->toHaveCount(2)
        ->and($shipment->shipment_number)->toStartWith('SHP-');
});

it('processes full chain from quote to invoice and shipment', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
        'subtotal' => 500,
        'discount_amount' => 0,
        'tax_amount' => 135,
        'total' => 635,
    ]);

    QuoteItem::factory()->count(3)->create(['quote_id' => $quote->id]);

    $result = $this->service->processFullChain($quote, 'GLS');

    expect($result)->toHaveKeys(['order', 'invoice', 'shipment'])
        ->and($result['order']->quote_id)->toBe($quote->id)
        ->and($result['invoice']->order_id)->toBe($result['order']->id)
        ->and($result['shipment']->order_id)->toBe($result['order']->id)
        ->and($result['order']->orderItems)->toHaveCount(3)
        ->and($result['invoice']->invoiceItems)->toHaveCount(3)
        ->and($result['shipment']->items)->toHaveCount(3);
});

it('preserves references through the chain', function (): void {
    $quote = Quote::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => QuoteStatus::Accepted,
    ]);

    QuoteItem::factory()->create(['quote_id' => $quote->id]);

    $result = $this->service->processFullChain($quote, 'FedEx');

    // Quote → Order reference
    expect($result['order']->quote_id)->toBe($quote->id);

    // Order → Invoice reference
    expect($result['invoice']->order_id)->toBe($result['order']->id);

    // Order → Shipment reference
    expect($result['shipment']->order_id)->toBe($result['order']->id);

    // All should belong to same customer
    expect($result['order']->customer_id)->toBe($this->customer->id)
        ->and($result['invoice']->customer_id)->toBe($this->customer->id)
        ->and($result['shipment']->customer_id)->toBe($this->customer->id);
});
