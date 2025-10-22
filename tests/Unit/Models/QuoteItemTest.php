<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $item = QuoteItem::factory()->create();

    expect($item)->toBeInstanceOf(QuoteItem::class);
});

it('has fillable attributes', function (): void {
    $quote = Quote::factory()->create();
    $product = Product::factory()->create();

    $item = QuoteItem::factory()->create([
        'quote_id' => $quote->id,
        'product_id' => $product->id,
        'description' => 'Test Product',
        'quantity' => 5.00,
        'unit_price' => 100.00,
        'discount_percent' => 10.00,
        'discount_amount' => 50.00,
        'tax_rate' => 27.00,
        'total' => 571.50,
    ]);

    expect($item->quote_id)->toBe($quote->id)
        ->and($item->product_id)->toBe($product->id)
        ->and($item->description)->toBe('Test Product')
        ->and($item->quantity)->toBe('5.00')
        ->and($item->unit_price)->toBe('100.00')
        ->and($item->discount_percent)->toBe('10.00')
        ->and($item->discount_amount)->toBe('50.00')
        ->and($item->tax_rate)->toBe('27.00')
        ->and($item->total)->toBe('571.50');
});

it('casts numeric fields to decimal', function (): void {
    $item = QuoteItem::factory()->create([
        'quantity' => 5.00,
        'unit_price' => 100.00,
        'discount_percent' => 10.00,
        'discount_amount' => 50.00,
        'tax_rate' => 27.00,
        'total' => 571.50,
    ]);

    expect($item->quantity)->toBe('5.00')
        ->and($item->unit_price)->toBe('100.00')
        ->and($item->discount_percent)->toBe('10.00')
        ->and($item->discount_amount)->toBe('50.00')
        ->and($item->tax_rate)->toBe('27.00')
        ->and($item->total)->toBe('571.50');
});

it('has quote relationship', function (): void {
    $quote = Quote::factory()->create();
    $item = QuoteItem::factory()->create(['quote_id' => $quote->id]);

    expect($item->quote)->toBeInstanceOf(Quote::class)
        ->and($item->quote->id)->toBe($quote->id);
});

it('has product relationship', function (): void {
    $product = Product::factory()->create();
    $item = QuoteItem::factory()->create(['product_id' => $product->id]);

    expect($item->product)->toBeInstanceOf(Product::class)
        ->and($item->product->id)->toBe($product->id);
});
