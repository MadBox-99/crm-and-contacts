<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\CustomerContact;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $contact = CustomerContact::factory()->create();

    expect($contact)->toBeInstanceOf(CustomerContact::class)
        ->and($contact->name)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $contact = CustomerContact::factory()->create([
        'customer_id' => $customer->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+36301234567',
        'position' => 'CEO',
        'is_primary' => true,
    ]);

    expect($contact->customer_id)->toBe($customer->id)
        ->and($contact->name)->toBe('John Doe')
        ->and($contact->email)->toBe('john@example.com')
        ->and($contact->phone)->toBe('+36301234567')
        ->and($contact->position)->toBe('CEO')
        ->and($contact->is_primary)->toBeTrue();
});

it('casts is_primary to boolean', function (): void {
    $contact = CustomerContact::factory()->create(['is_primary' => 1]);

    expect($contact->is_primary)->toBeTrue()
        ->and($contact->is_primary)->toBeBool();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);

    expect($contact->customer)->toBeInstanceOf(Customer::class)
        ->and($contact->customer->id)->toBe($customer->id);
});
