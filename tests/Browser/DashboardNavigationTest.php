<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'navigation@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitHome(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'navigation@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug);
}

it('navigates to sales dashboard via sidebar', function (): void {
    $page = loginAndVisitHome($this);

    $page->click('Sales Dashboard')
        ->wait(1)
        ->assertPathIs('/app/'.$this->team->slug.'/sales-dashboard')
        ->assertSee('Sales Dashboard')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('navigation/sales-dashboard'), fullPage: true);
});

it('navigates to customer list via sidebar', function (): void {
    $page = loginAndVisitHome($this);

    $page->click('Customer List')
        ->wait(1)
        ->assertPathIs('/app/'.$this->team->slug.'/customers')
        ->assertSee('Customers')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('navigation/customer-list'), fullPage: true);
});

it('navigates to opportunities via sidebar', function (): void {
    $page = loginAndVisitHome($this);

    $page->click('Leads / Opportunities')
        ->wait(1)
        ->assertPathIs('/app/'.$this->team->slug.'/opportunities')
        ->assertSee('Opportunities')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('navigation/opportunities'), fullPage: true);
});

it('redirects guest to login page', function (): void {
    $page = visit('/app/'.$this->team->slug);

    $page->assertPathIs('/app/login')
        ->assertSee('Sign in')
        ->screenshot(fullPage: true, filename: screenshotPath('navigation/guest-redirect'));
});
