<?php

declare(strict_types=1);

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    // Create an admin user with all permissions
    $this->user = User::factory()->create();

    // Create necessary permissions
    Permission::query()->firstOrCreate(['name' => 'view_any_customer']);
    Permission::query()->firstOrCreate(['name' => 'view_customer']);
    Permission::query()->firstOrCreate(['name' => 'create_customer']);
    Permission::query()->firstOrCreate(['name' => 'update_customer']);
    Permission::query()->firstOrCreate(['name' => 'delete_customer']);

    // Give user all permissions
    $this->user->givePermissionTo([
        'view_any_customer',
        'view_customer',
        'create_customer',
        'update_customer',
        'delete_customer',
    ]);

    $this->actingAs($this->user);
});

it('can render customer list page', function (): void {
    livewire(ListCustomers::class)
        ->assertSuccessful();
});

it('can list customers', function (): void {
    $customers = Customer::factory()->count(3)->create();

    livewire(ListCustomers::class)
        ->assertCanSeeTableRecords($customers);
});

it('can search customers by name', function (): void {
    $customers = Customer::factory()->count(3)->create();
    $customerToFind = $customers->first();

    livewire(ListCustomers::class)
        ->searchTable($customerToFind->name)
        ->assertCanSeeTableRecords([$customerToFind])
        ->assertCanNotSeeTableRecords($customers->skip(1));
});

it('can render create customer page', function (): void {
    livewire(CreateCustomer::class)
        ->assertSuccessful();
});

it('can render edit customer page', function (): void {
    $customer = Customer::factory()->create();

    livewire(EditCustomer::class, ['record' => $customer->id])
        ->assertSuccessful();
});

it('can retrieve customer data for editing', function (): void {
    $customer = Customer::factory()->create();

    livewire(EditCustomer::class, ['record' => $customer->id])
        ->assertSchemaStateSet([
            'unique_identifier' => $customer->unique_identifier,
            'name' => $customer->name,
            'type' => $customer->type,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ]);
});

it('can delete a customer', function (): void {
    $customer = Customer::factory()->create();

    livewire(EditCustomer::class, ['record' => $customer->id])
        ->callAction('delete');

    $this->assertSoftDeleted($customer);
});

it('cannot access list page without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(ListCustomers::class)
        ->assertForbidden();
});

it('cannot access create page without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(CreateCustomer::class)
        ->assertForbidden();
});

it('cannot access edit page without permission', function (): void {
    $customer = Customer::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(EditCustomer::class, ['record' => $customer->id])
        ->assertForbidden();
});
