<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->invoiceService = resolve(InvoiceService::class);
    $this->customer = Customer::factory()->create();
});

it('creates invoice from order with correct data', function (): void {
    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'subtotal' => 1000,
        'discount_amount' => 100,
        'tax_amount' => 243,
        'total' => 1143,
    ]);

    OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

    $invoice = $this->invoiceService->createFromOrder($order);

    expect($invoice->customer_id)->toBe($this->customer->id)
        ->and($invoice->order_id)->toBe($order->id)
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and($invoice->subtotal)->toBe('1000.00')
        ->and($invoice->total)->toBe('1143.00')
        ->and($invoice->invoice_number)->toStartWith('INV-');
});

it('creates invoice items matching order items', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->count(5)->create(['order_id' => $order->id]);

    $invoice = $this->invoiceService->createFromOrder($order);

    expect($invoice->invoiceItems)->toHaveCount(5);
});

it('generates unique invoice numbers', function (): void {
    $order1 = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->create(['order_id' => $order1->id]);
    $order2 = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->create(['order_id' => $order2->id]);

    $invoice1 = $this->invoiceService->createFromOrder($order1);
    $invoice2 = $this->invoiceService->createFromOrder($order2);

    expect($invoice1->invoice_number)->not->toBe($invoice2->invoice_number);
});

it('sets correct due date based on parameter', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id]);

    $invoice = $this->invoiceService->createFromOrder($order, 15);

    expect((int) $invoice->issue_date->diffInDays($invoice->due_date))->toBe(15);
});

it('generates PDF for invoice', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id]);
    $invoice = $this->invoiceService->createFromOrder($order);

    $path = $this->invoiceService->generatePdf($invoice);

    expect($path)->toBeString()
        ->and(file_exists($path))->toBeTrue();

    // Cleanup
    @unlink($path);
});

it('prepares NAV XML with correct structure', function (): void {
    $order = Order::factory()->create(['customer_id' => $this->customer->id]);
    OrderItem::factory()->count(2)->create(['order_id' => $order->id]);
    $invoice = $this->invoiceService->createFromOrder($order);

    $xml = $this->invoiceService->prepareNavXml($invoice);

    expect($xml)->toBeString()
        ->and($xml)->toContain('<?xml')
        ->and($xml)->toContain('InvoiceData')
        ->and($xml)->toContain($invoice->invoice_number)
        ->and($xml)->toContain('invoiceLines')
        ->and($xml)->toContain('invoiceSummary');
});
