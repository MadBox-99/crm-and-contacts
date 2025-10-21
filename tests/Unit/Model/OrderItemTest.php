<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $item = OrderItem::factory()->create();

    expect($item)->toBeInstanceOf(OrderItem::class);
});

it('has fillable attributes', function (): void {
    $order = Order::factory()->create();
    $product = Product::factory()->create();

    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'description' => 'Test Product',
        'quantity' => 5,
        'unit_price' => 100.00,
        'discount_amount' => 10.00,
        'tax_rate' => 27.00,
        'total' => 563.50,
    ]);

    expect($item->order_id)->toBe($order->id)
        ->and($item->product_id)->toBe($product->id)
        ->and($item->description)->toBe('Test Product')
        ->and($item->quantity)->toBe('5.00')
        ->and($item->unit_price)->toBe('100.00')
        ->and($item->discount_amount)->toBe('10.00')
        ->and($item->tax_rate)->toBe('27.00')
        ->and($item->total)->toBe('563.50');
});

it('casts numeric fields to decimal', function (): void {
    $item = OrderItem::factory()->create([
        'quantity' => 5.00,
        'unit_price' => 100.00,
        'discount_amount' => 10.00,
        'tax_rate' => 27.00,
        'total' => 563.50,
    ]);

    expect($item->quantity)->toBe('5.00')
        ->and($item->unit_price)->toBe('100.00')
        ->and($item->discount_amount)->toBe('10.00')
        ->and($item->tax_rate)->toBe('27.00')
        ->and($item->total)->toBe('563.50');
});

it('has order relationship', function (): void {
    $order = Order::factory()->create();
    $item = OrderItem::factory()->create(['order_id' => $order->id]);

    expect($item->order)->toBeInstanceOf(Order::class)
        ->and($item->order->id)->toBe($order->id);
});

it('has product relationship', function (): void {
    $product = Product::factory()->create();
    $item = OrderItem::factory()->create(['product_id' => $product->id]);

    expect($item->product)->toBeInstanceOf(Product::class)
        ->and($item->product->id)->toBe($product->id);
});
