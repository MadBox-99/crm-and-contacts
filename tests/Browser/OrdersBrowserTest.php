<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Order;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'orders@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitOrders(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'orders@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/orders');
}

it('renders the orders list page', function (): void {
    $page = loginAndVisitOrders($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/orders')
        ->assertSee('Orders')
        ->assertSee('New Order')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('orders/list'), fullPage: true);
});

it('displays seeded orders in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'Order Customer Inc',
    ]);

    Order::factory()->for($customer)->for($this->team)->create([
        'order_number' => 'ORD-TEST-001',
    ]);

    $page = loginAndVisitOrders($this);

    $page->assertSee('ORD-TEST-001')
        ->assertSee('Order Customer Inc')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('orders/with-data'), fullPage: true);
});

it('renders the order view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $order = Order::factory()->for($customer)->for($this->team)->create([
        'order_number' => 'ORD-VIEW-001',
    ]);

    loginAndVisitOrders($this);

    $page = visit('/app/'.$this->team->slug.'/orders/'.$order->id);

    $page->assertSee('ORD-VIEW-001')
        ->assertSee('Order details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('orders/view'));
});

it('renders the order edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $order = Order::factory()->for($customer)->for($this->team)->create();

    loginAndVisitOrders($this);

    $page = visit('/app/'.$this->team->slug.'/orders/'.$order->id.'/edit');

    $page->assertSee('Edit Order')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('orders/edit'));
});
