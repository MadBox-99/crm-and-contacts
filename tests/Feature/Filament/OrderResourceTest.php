<?php

declare(strict_types=1);

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Spatie\Permission\Models\Permission;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    Permission::query()->firstOrCreate(['name' => 'view_any_order']);
    Permission::query()->firstOrCreate(['name' => 'view_order']);
    Permission::query()->firstOrCreate(['name' => 'create_order']);
    Permission::query()->firstOrCreate(['name' => 'update_order']);
    Permission::query()->firstOrCreate(['name' => 'delete_order']);

    $this->user->givePermissionTo([
        'view_any_order',
        'view_order',
        'create_order',
        'update_order',
        'delete_order',
    ]);

    $this->actingAs($this->user);
});

it('can render order list page', function (): void {
    livewire(ListOrders::class)
        ->assertSuccessful();
});

it('can list orders', function (): void {
    $orders = Order::factory()->count(3)->create();

    livewire(ListOrders::class)
        ->assertCanSeeTableRecords($orders);
});

it('can render create order page', function (): void {
    livewire(CreateOrder::class)
        ->assertSuccessful();
});

it('can render edit order page', function (): void {
    $order = Order::factory()->create();

    livewire(EditOrder::class, ['record' => $order->id])
        ->assertSuccessful();
});

it('cannot access list page without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(ListOrders::class)
        ->assertForbidden();
});

it('cannot access create page without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(CreateOrder::class)
        ->assertForbidden();
});

it('cannot access edit page without permission', function (): void {
    $order = Order::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    livewire(EditOrder::class, ['record' => $order->id])
        ->assertForbidden();
});
