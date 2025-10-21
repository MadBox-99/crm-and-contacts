<?php

declare(strict_types=1);

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    // Reset cached permissions
    app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('allows users with view_any_customer permission to view any customers', function (): void {
    $user = User::factory()->create();
    Permission::create(['name' => PermissionEnum::ViewAnyCustomer]);
    $user->givePermissionTo(PermissionEnum::ViewAnyCustomer);

    expect($user->can('viewAny', Customer::class))->toBeTrue();
});

it('denies users without view_any_customer permission to view any customers', function (): void {
    $user = User::factory()->create();

    expect($user->can('viewAny', Customer::class))->toBeFalse();
});

it('allows users with view_customer permission to view a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Permission::create(['name' => PermissionEnum::ViewCustomer]);
    $user->givePermissionTo(PermissionEnum::ViewCustomer);

    expect($user->can('view', $customer))->toBeTrue();
});

it('denies users without view_customer permission to view a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    expect($user->can('view', $customer))->toBeFalse();
});

it('allows users with create_customer permission to create customers', function (): void {
    $user = User::factory()->create();
    Permission::create(['name' => PermissionEnum::CreateCustomer]);
    $user->givePermissionTo(PermissionEnum::CreateCustomer);

    expect($user->can('create', Customer::class))->toBeTrue();
});

it('denies users without create_customer permission to create customers', function (): void {
    $user = User::factory()->create();

    expect($user->can('create', Customer::class))->toBeFalse();
});

it('allows users with update_customer permission to update a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Permission::create(['name' => PermissionEnum::UpdateCustomer]);
    $user->givePermissionTo(PermissionEnum::UpdateCustomer);

    expect($user->can('update', $customer))->toBeTrue();
});

it('denies users without update_customer permission to update a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    expect($user->can('update', $customer))->toBeFalse();
});

it('allows users with delete_customer permission to delete a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Permission::create(['name' => PermissionEnum::DeleteCustomer]);
    $user->givePermissionTo(PermissionEnum::DeleteCustomer);

    expect($user->can('delete', $customer))->toBeTrue();
});

it('denies users without delete_customer permission to delete a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    expect($user->can('delete', $customer))->toBeFalse();
});

it('allows users with restore_customer permission to restore a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Permission::create(['name' => PermissionEnum::RestoreCustomer]);
    $user->givePermissionTo(PermissionEnum::RestoreCustomer);

    expect($user->can('restore', $customer))->toBeTrue();
});

it('denies users without restore_customer permission to restore a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    expect($user->can('restore', $customer))->toBeFalse();
});

it('allows users with force_delete_customer permission to force delete a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();
    Permission::create(['name' => PermissionEnum::ForceDeleteCustomer]);
    $user->givePermissionTo(PermissionEnum::ForceDeleteCustomer);

    expect($user->can('forceDelete', $customer))->toBeTrue();
});

it('denies users without force_delete_customer permission to force delete a customer', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    expect($user->can('forceDelete', $customer))->toBeFalse();
});

it('grants all customer permissions to Admin role', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    // Run the seeder to set up roles and permissions
    $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);

    $user->assignRole(RoleEnum::Admin);

    expect($user->can('viewAny', Customer::class))->toBeTrue();
    expect($user->can('view', $customer))->toBeTrue();
    expect($user->can('create', Customer::class))->toBeTrue();
    expect($user->can('update', $customer))->toBeTrue();
    expect($user->can('delete', $customer))->toBeTrue();
    expect($user->can('restore', $customer))->toBeTrue();
    expect($user->can('forceDelete', $customer))->toBeTrue();
});

it('grants specific customer permissions to Manager role', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);

    $user->assignRole(RoleEnum::Manager);

    expect($user->can('viewAny', Customer::class))->toBeTrue();
    expect($user->can('view', $customer))->toBeTrue();
    expect($user->can('create', Customer::class))->toBeTrue();
    expect($user->can('update', $customer))->toBeTrue();
    expect($user->can('delete', $customer))->toBeFalse();
});

it('grants specific customer permissions to Sales Representative role', function (): void {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);

    $user->assignRole(RoleEnum::SalesRepresentative);

    expect($user->can('viewAny', Customer::class))->toBeTrue();
    expect($user->can('view', $customer))->toBeTrue();
    expect($user->can('create', Customer::class))->toBeTrue();
    expect($user->can('update', $customer))->toBeTrue();
    expect($user->can('delete', $customer))->toBeFalse();
});
