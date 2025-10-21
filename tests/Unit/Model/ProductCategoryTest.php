<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $category = ProductCategory::factory()->create();

    expect($category)->toBeInstanceOf(ProductCategory::class)
        ->and($category->name)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $parent = ProductCategory::factory()->create();
    $category = ProductCategory::factory()->create([
        'name' => 'Test Category',
        'parent_id' => $parent->id,
        'description' => 'Test Description',
    ]);

    expect($category->name)->toBe('Test Category')
        ->and($category->parent_id)->toBe($parent->id)
        ->and($category->description)->toBe('Test Description');
});

it('has parent relationship', function (): void {
    $parent = ProductCategory::factory()->create();
    $child = ProductCategory::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent)->toBeInstanceOf(ProductCategory::class)
        ->and($child->parent->id)->toBe($parent->id);
});

it('has children relationship', function (): void {
    $parent = ProductCategory::factory()->create();
    $child1 = ProductCategory::factory()->create(['parent_id' => $parent->id]);
    $child2 = ProductCategory::factory()->create(['parent_id' => $parent->id]);

    expect($parent->children)->toHaveCount(2);
});

it('has products relationship', function (): void {
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    expect($category->products)->toHaveCount(1)
        ->and($category->products->first()->id)->toBe($product->id);
});
