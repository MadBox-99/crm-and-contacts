<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Quote;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'quotes@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitQuotes(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'quotes@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/quotes');
}

it('renders the quotes list page', function (): void {
    $page = loginAndVisitQuotes($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/quotes')
        ->assertSee('Quotes')
        ->assertSee('New Quote')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('quotes/list'), fullPage: true);
});

it('displays seeded quotes in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'Quote Customer Ltd',
    ]);

    Quote::factory()->for($customer)->for($this->team)->draft()->create([
        'quote_number' => 'QT-TEST-001',
    ]);

    $page = loginAndVisitQuotes($this);

    $page->assertSee('QT-TEST-001')
        ->assertSee('Quote Customer Ltd')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('quotes/with-data'), fullPage: true);
});

it('renders the quote view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $quote = Quote::factory()->for($customer)->for($this->team)->draft()->create([
        'quote_number' => 'QT-VIEW-001',
    ]);

    loginAndVisitQuotes($this);

    $page = visit('/app/'.$this->team->slug.'/quotes/'.$quote->id);

    $page->assertSee('QT-VIEW-001')
        ->assertSee('Quote details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('quotes/view'));
});

it('renders the quote edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $quote = Quote::factory()->for($customer)->for($this->team)->draft()->create();

    loginAndVisitQuotes($this);

    $page = visit('/app/'.$this->team->slug.'/quotes/'.$quote->id.'/edit');

    $page->assertSee('Edit Quote')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('quotes/edit'));
});
