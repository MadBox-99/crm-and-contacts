<?php

declare(strict_types=1);

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});

it('hides password and remember_token', function (): void {
    $user = User::factory()->create();

    $array = $user->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

it('casts email_verified_at to datetime', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->toBeInstanceOf(DateTimeImmutable::class);
});

it('can access panel', function (): void {
    $user = User::factory()->create();
    $panel = Mockery::mock(Panel::class);

    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('can check role using RoleEnum', function (): void {
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Admin->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Support->value]);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin);

    expect($user->hasRole(RoleEnum::Admin))->toBeTrue()
        ->and($user->hasRole(RoleEnum::Support))->toBeFalse();
});

it('can check role using string', function (): void {
    Spatie\Permission\Models\Role::create(['name' => 'admin']);
    Spatie\Permission\Models\Role::create(['name' => 'user']);

    $user = User::factory()->create();
    $user->assignRole('admin');

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('user'))->toBeFalse();
});

it('can check if user has any role', function (): void {
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Admin->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Manager->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Support->value]);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin);

    expect($user->hasAnyRole(RoleEnum::Admin, RoleEnum::Manager))->toBeTrue()
        ->and($user->hasAnyRole(RoleEnum::Support, RoleEnum::Manager))->toBeFalse();
});

it('can check if user has all roles', function (): void {
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Admin->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Manager->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Support->value]);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin);
    $user->assignRole(RoleEnum::Manager);

    expect($user->hasAllRoles([RoleEnum::Admin, RoleEnum::Manager]))->toBeTrue()
        ->and($user->hasAllRoles([RoleEnum::Admin, RoleEnum::Support]))->toBeFalse();
});

it('can assign role using RoleEnum', function (): void {
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Admin->value]);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin);

    expect($user->hasRole(RoleEnum::Admin))->toBeTrue();
});

it('can assign multiple roles', function (): void {
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Admin->value]);
    Spatie\Permission\Models\Role::create(['name' => RoleEnum::Manager->value]);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin, RoleEnum::Manager);

    expect($user->hasRole(RoleEnum::Admin))->toBeTrue()
        ->and($user->hasRole(RoleEnum::Manager))->toBeTrue();
});

it('can give permission using PermissionEnum', function (): void {
    Spatie\Permission\Models\Permission::create(['name' => PermissionEnum::ViewCustomer->value]);

    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ViewCustomer);

    expect($user->hasPermissionTo(PermissionEnum::ViewCustomer->value))->toBeTrue();
});

it('can give multiple permissions', function (): void {
    Spatie\Permission\Models\Permission::create(['name' => PermissionEnum::ViewCustomer->value]);
    Spatie\Permission\Models\Permission::create(['name' => PermissionEnum::CreateCustomer->value]);

    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ViewCustomer, PermissionEnum::CreateCustomer);

    expect($user->hasPermissionTo(PermissionEnum::ViewCustomer->value))->toBeTrue()
        ->and($user->hasPermissionTo(PermissionEnum::CreateCustomer->value))->toBeTrue();
});
