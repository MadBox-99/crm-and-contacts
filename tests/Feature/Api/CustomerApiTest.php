<?php

declare(strict_types=1);

use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    // Create necessary permissions
    Permission::query()->firstOrCreate(['name' => 'view_any_customer']);
    Permission::query()->firstOrCreate(['name' => 'view_customer']);
    Permission::query()->firstOrCreate(['name' => 'create_customer']);
    Permission::query()->firstOrCreate(['name' => 'update_customer']);
    Permission::query()->firstOrCreate(['name' => 'delete_customer']);

    // Give user all customer permissions
    $this->user->givePermissionTo([
        'view_any_customer',
        'view_customer',
        'create_customer',
        'update_customer',
        'delete_customer',
    ]);

    $this->token = $this->user->createToken('test-device')->plainTextToken;
});

it('can list customers', function (): void {
    Customer::factory()->count(3)->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/v1/customers');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'type', 'email', 'phone', 'is_active', 'created_at'],
            ],
            'links',
            'meta',
        ]);
});

it('can create a customer', function (): void {
    $customerData = [
        'unique_identifier' => 'CUST-API-001',
        'name' => 'Test Customer',
        'type' => CustomerType::Company->value,
        'email' => 'test@api.com',
        'phone' => '+36301234567',
        'is_active' => true,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->postJson('/api/v1/customers', $customerData);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'unique_identifier' => 'CUST-API-001',
                'name' => 'Test Customer',
                'type' => CustomerType::Company->value,
            ],
        ]);

    $this->assertDatabaseHas('customers', [
        'unique_identifier' => 'CUST-API-001',
        'name' => 'Test Customer',
    ]);
});

it('validates customer creation', function (): void {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->postJson('/api/v1/customers', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['unique_identifier', 'name', 'type']);
});

it('can show a customer', function (): void {
    $customer = Customer::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/v1/customers/'.$customer->id);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
            ],
        ]);
});

it('can update a customer', function (): void {
    $customer = Customer::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->putJson('/api/v1/customers/'.$customer->id, [
        'name' => 'Updated Name',
    ]);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'name' => 'Updated Name',
            ],
        ]);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a customer', function (): void {
    $customer = Customer::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->deleteJson('/api/v1/customers/'.$customer->id);

    $response->assertOk()
        ->assertJson(['message' => 'Customer deleted successfully']);

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});

it('cannot access customers without authentication', function (): void {
    $response = $this->getJson('/api/v1/customers');

    $response->assertUnauthorized();
});

it('cannot access customers without permission', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-device')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/v1/customers');

    $response->assertForbidden();
});
