<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $address = CustomerAddress::factory()->create();

    expect($address)->toBeInstanceOf(CustomerAddress::class)
        ->and($address->city)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $address = CustomerAddress::factory()->create([
        'customer_id' => $customer->id,
        'type' => 'billing',
        'country' => 'Hungary',
        'postal_code' => '1011',
        'city' => 'Budapest',
        'street' => 'Main Street',
        'building_number' => '42',
        'floor' => '3',
        'door' => '12',
        'is_default' => true,
    ]);

    expect($address->customer_id)->toBe($customer->id)
        ->and($address->type)->toBe('billing')
        ->and($address->country)->toBe('Hungary')
        ->and($address->postal_code)->toBe('1011')
        ->and($address->city)->toBe('Budapest')
        ->and($address->street)->toBe('Main Street')
        ->and($address->building_number)->toBe('42')
        ->and($address->floor)->toBe('3')
        ->and($address->door)->toBe('12')
        ->and($address->is_default)->toBeTrue();
});

it('casts is_default to boolean', function (): void {
    $address = CustomerAddress::factory()->create(['is_default' => 1]);

    expect($address->is_default)->toBeTrue()
        ->and($address->is_default)->toBeBool();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $address = CustomerAddress::factory()->create(['customer_id' => $customer->id]);

    expect($address->customer)->toBeInstanceOf(Customer::class)
        ->and($address->customer->id)->toBe($customer->id);
});
