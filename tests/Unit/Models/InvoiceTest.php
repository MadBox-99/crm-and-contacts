<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $invoice = Invoice::factory()->create();

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->invoice_number)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $invoice = Invoice::factory()->create([
        'customer_id' => $customer->id,
        'order_id' => $order->id,
        'invoice_number' => 'INV-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => InvoiceStatus::Draft,
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
        'notes' => 'Test notes',
        'paid_at' => null,
    ]);

    expect($invoice->customer_id)->toBe($customer->id)
        ->and($invoice->order_id)->toBe($order->id)
        ->and($invoice->invoice_number)->toBe('INV-001')
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and($invoice->subtotal)->toBe('100.00')
        ->and($invoice->discount_amount)->toBe('10.00')
        ->and($invoice->tax_amount)->toBe('24.30')
        ->and($invoice->total)->toBe('114.30')
        ->and($invoice->notes)->toBe('Test notes')
        ->and($invoice->paid_at)->toBeNull();
});

it('casts date fields to date', function (): void {
    $invoice = Invoice::factory()->create([
        'issue_date' => '2025-01-15',
        'due_date' => '2025-02-15',
    ]);

    expect($invoice->issue_date)->toBeInstanceOf(DateTimeInterface::class)
        ->and($invoice->due_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts paid_at to datetime', function (): void {
    $invoice = Invoice::factory()->create(['paid_at' => now()]);

    expect($invoice->paid_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts money fields to decimal', function (): void {
    $invoice = Invoice::factory()->create([
        'subtotal' => 100.00,
        'discount_amount' => 10.00,
        'tax_amount' => 24.30,
        'total' => 114.30,
    ]);

    expect($invoice->subtotal)->toBe('100.00')
        ->and($invoice->discount_amount)->toBe('10.00')
        ->and($invoice->tax_amount)->toBe('24.30')
        ->and($invoice->total)->toBe('114.30');
});

it('uses soft deletes', function (): void {
    $invoice = Invoice::factory()->create();
    $invoice->delete();

    expect($invoice->trashed())->toBeTrue();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

    expect($invoice->customer)->toBeInstanceOf(Customer::class)
        ->and($invoice->customer->id)->toBe($customer->id);
});

it('has order relationship', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);
    $invoice = Invoice::factory()->create([
        'customer_id' => $customer->id,
        'order_id' => $order->id,
    ]);

    expect($invoice->order)->toBeInstanceOf(Order::class)
        ->and($invoice->order->id)->toBe($order->id);
});
