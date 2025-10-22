<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->not->toBeEmpty()
        ->and($product->sku)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'description' => 'Test Description',
        'category_id' => $category->id,
        'unit_price' => 99.99,
        'tax_rate' => 27.00,
        'is_active' => true,
    ]);

    expect($product->name)->toBe('Test Product')
        ->and($product->sku)->toBe('TEST-001')
        ->and($product->description)->toBe('Test Description')
        ->and($product->category_id)->toBe($category->id)
        ->and($product->unit_price)->toBe('99.99')
        ->and($product->tax_rate)->toBe('27.00')
        ->and($product->is_active)->toBeTrue();
});

it('casts unit_price to decimal', function (): void {
    $product = Product::factory()->create(['unit_price' => 99.99]);

    expect($product->unit_price)->toBe('99.99');
});

it('casts tax_rate to decimal', function (): void {
    $product = Product::factory()->create(['tax_rate' => 27.00]);

    expect($product->tax_rate)->toBe('27.00');
});

it('casts is_active to boolean', function (): void {
    $product = Product::factory()->create(['is_active' => 1]);

    expect($product->is_active)->toBeTrue()
        ->and($product->is_active)->toBeBool();
});

it('uses soft deletes', function (): void {
    $product = Product::factory()->create();
    $product->delete();

    expect($product->trashed())->toBeTrue();
});

it('has category relationship', function (): void {
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    expect($product->category)->toBeInstanceOf(ProductCategory::class)
        ->and($product->category->id)->toBe($category->id);
});
