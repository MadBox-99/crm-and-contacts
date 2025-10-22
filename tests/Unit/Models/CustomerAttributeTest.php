<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\CustomerAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $attribute = CustomerAttribute::factory()->create();

    expect($attribute)->toBeInstanceOf(CustomerAttribute::class)
        ->and($attribute->attribute_key)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $attribute = CustomerAttribute::factory()->create([
        'customer_id' => $customer->id,
        'attribute_key' => 'vat_exemption',
        'attribute_value' => 'true',
    ]);

    expect($attribute->customer_id)->toBe($customer->id)
        ->and($attribute->attribute_key)->toBe('vat_exemption')
        ->and($attribute->attribute_value)->toBe('true');
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $attribute = CustomerAttribute::factory()->create(['customer_id' => $customer->id]);

    expect($attribute->customer)->toBeInstanceOf(Customer::class)
        ->and($attribute->customer->id)->toBe($customer->id);
});
