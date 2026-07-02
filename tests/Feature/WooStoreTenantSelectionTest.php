<?php

declare(strict_types=1);

use App\Enums\Role as RoleEnum;
use App\Models\User;
use Madbox99\FilamentWooCommerce\Filament\Resources\WooStoreResource\Schemas\WooStoreForm;

it('lets admins choose a WooStore tenant', function (): void {
    $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::Admin);

    $this->actingAs($user);

    expect(WooStoreForm::canSelectTenant())->toBeTrue();
});

it('prevents regular users from choosing a WooStore tenant', function (): void {
    $this->actingAs(User::factory()->create());

    expect(WooStoreForm::canSelectTenant())->toBeFalse();
});

it('denies WooStore tenant selection to guests', function (): void {
    expect(WooStoreForm::canSelectTenant())->toBeFalse();
});
