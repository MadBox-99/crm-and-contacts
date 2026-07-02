<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'invoices@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitInvoices(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'invoices@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/invoices');
}

it('renders the invoices list page', function (): void {
    $page = loginAndVisitInvoices($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/invoices')
        ->assertSee('Invoices')
        ->assertSee('New Invoice')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('invoices/list'), fullPage: true);
});

it('displays seeded invoices in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'Invoice Customer Kft',
    ]);

    Invoice::factory()->for($customer)->for($this->team)->create([
        'invoice_number' => 'INV-TEST-001',
        'order_id' => null,
    ]);

    $page = loginAndVisitInvoices($this);

    $page->assertSee('INV-TEST-001')
        ->assertSee('Invoice Customer Kft')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('invoices/with-data'), fullPage: true);
});

it('renders the invoice view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $invoice = Invoice::factory()->for($customer)->for($this->team)->create([
        'invoice_number' => 'INV-VIEW-001',
        'order_id' => null,
    ]);

    loginAndVisitInvoices($this);

    $page = visit('/app/'.$this->team->slug.'/invoices/'.$invoice->id);

    $page->assertSee('INV-VIEW-001')
        ->assertSee('Invoice details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('invoices/view'));
});

it('renders the invoice edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $invoice = Invoice::factory()->for($customer)->for($this->team)->create([
        'order_id' => null,
    ]);

    loginAndVisitInvoices($this);

    $page = visit('/app/'.$this->team->slug.'/invoices/'.$invoice->id.'/edit');

    $page->assertSee('Edit Invoice')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('invoices/edit'));
});
