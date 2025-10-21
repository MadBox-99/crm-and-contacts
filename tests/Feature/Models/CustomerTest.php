<?php

declare(strict_types=1);

use App\Enums\CustomerType;
use App\Models\Customer;

it('can create a customer using factory', function (): void {
    $customer = Customer::factory()->create();

    expect($customer)->toBeInstanceOf(Customer::class);
    expect($customer->id)->not->toBeNull();
    expect($customer->name)->not->toBeNull();
});

it('can create B2B customer', function (): void {
    $customer = Customer::factory()->b2b()->create();

    expect($customer->type)->toBe(CustomerType::Company);
    expect($customer->tax_number)->not->toBeNull();
    expect($customer->registration_number)->not->toBeNull();
});

it('can create B2C customer', function (): void {
    $customer = Customer::factory()->b2c()->create();

    expect($customer->type)->toBe(CustomerType::Individual);
});

it('can create inactive customer', function (): void {
    $customer = Customer::factory()->inactive()->create();

    expect($customer->is_active)->toBeFalse();
});

it('casts is_active as boolean', function (): void {
    $customer = Customer::factory()->create(['is_active' => true]);

    expect($customer->is_active)->toBeTrue();
    expect($customer->is_active)->toBeBool();
});

it('can be soft deleted', function (): void {
    $customer = Customer::factory()->create();
    $customerId = $customer->id;

    $customer->delete();

    expect(Customer::query()->find($customerId))->toBeNull();
    expect(Customer::withTrashed()->find($customerId))->not->toBeNull();
});

it('can be restored after soft delete', function (): void {
    $customer = Customer::factory()->create();
    $customer->delete();

    $customer->restore();

    expect(Customer::query()->find($customer->id))->not->toBeNull();
});

it('has unique_identifier field', function (): void {
    $customer = Customer::factory()->create();

    expect($customer->unique_identifier)->not->toBeNull();
    expect($customer->unique_identifier)->toContain('CUST-');
});

it('enforces type enum values', function (): void {
    $customer = Customer::factory()->create();

    expect($customer->type)->toBeIn(CustomerType::cases());
});

it('can store contact information', function (): void {
    $customer = Customer::factory()->create([
        'email' => 'test@example.com',
        'phone' => '+36301234567',
    ]);

    expect($customer->email)->toBe('test@example.com');
    expect($customer->phone)->toBe('+36301234567');
});

it('can store notes', function (): void {
    $customer = Customer::factory()->create([
        'notes' => 'This is a test note',
    ]);

    expect($customer->notes)->toBe('This is a test note');
});
