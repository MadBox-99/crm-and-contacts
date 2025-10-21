<?php

declare(strict_types=1);

use App\Enums\DiscountType;
use App\Enums\DiscountValueType;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $discount = Discount::factory()->create();

    expect($discount)->toBeInstanceOf(Discount::class)
        ->and($discount->name)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    $discount = Discount::factory()->create([
        'name' => 'Summer Sale',
        'type' => DiscountType::Custom,
        'value_type' => DiscountValueType::Percentage,
        'value' => 20.00,
        'min_quantity' => 5.00,
        'min_value' => 1000.00,
        'valid_from' => now(),
        'valid_until' => now()->addDays(30),
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'is_active' => true,
        'description' => 'Summer discount',
    ]);

    expect($discount->name)->toBe('Summer Sale')
        ->and($discount->type)->toBe(DiscountType::Custom)
        ->and($discount->value_type)->toBe(DiscountValueType::Percentage)
        ->and($discount->value)->toBe('20.00')
        ->and($discount->min_quantity)->toBe('5.00')
        ->and($discount->min_value)->toBe('1000.00')
        ->and($discount->customer_id)->toBe($customer->id)
        ->and($discount->product_id)->toBe($product->id)
        ->and($discount->is_active)->toBeTrue()
        ->and($discount->description)->toBe('Summer discount');
});

it('casts numeric fields to decimal', function (): void {
    $discount = Discount::factory()->create([
        'value' => 20.00,
        'min_quantity' => 5.00,
        'min_value' => 1000.00,
    ]);

    expect($discount->value)->toBe('20.00')
        ->and($discount->min_quantity)->toBe('5.00')
        ->and($discount->min_value)->toBe('1000.00');
});

it('casts date fields to date', function (): void {
    $discount = Discount::factory()->create([
        'valid_from' => '2025-01-01',
        'valid_until' => '2025-12-31',
    ]);

    expect($discount->valid_from)->toBeInstanceOf(DateTimeInterface::class)
        ->and($discount->valid_until)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts is_active to boolean', function (): void {
    $discount = Discount::factory()->create(['is_active' => 1]);

    expect($discount->is_active)->toBeTrue()
        ->and($discount->is_active)->toBeBool();
});

it('uses soft deletes', function (): void {
    $discount = Discount::factory()->create();
    $discount->delete();

    expect($discount->trashed())->toBeTrue();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $discount = Discount::factory()->create(['customer_id' => $customer->id]);

    expect($discount->customer)->toBeInstanceOf(Customer::class)
        ->and($discount->customer->id)->toBe($customer->id);
});

it('has product relationship', function (): void {
    $product = Product::factory()->create();
    $discount = Discount::factory()->create(['product_id' => $product->id]);

    expect($discount->product)->toBeInstanceOf(Product::class)
        ->and($discount->product->id)->toBe($product->id);
});
