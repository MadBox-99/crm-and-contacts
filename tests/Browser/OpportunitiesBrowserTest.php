<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'opportunities@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
    $this->customer = Customer::factory()->for($this->team)->create();
});

function loginAndVisitOpportunities(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'opportunities@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/opportunities');
}

it('renders the opportunities list page', function (): void {
    $page = loginAndVisitOpportunities($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/opportunities')
        ->assertSee('Opportunities')
        ->assertSee('New Opportunity')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('opportunities/list'), fullPage: true);
});

it('displays seeded opportunities in the table', function (): void {
    Opportunity::factory()->for($this->customer)->for($this->team)->create([
        'title' => 'Browser Test Deal',
        'stage' => 'lead',
        'assigned_to' => $this->user->id,
    ]);

    $page = loginAndVisitOpportunities($this);

    $page->assertSee('Browser Test Deal')
        ->assertSee($this->customer->name)
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('opportunities/with-data'), fullPage: true);
});

it('renders the opportunity edit page', function (): void {
    $opportunity = Opportunity::factory()->for($this->customer)->for($this->team)->create([
        'title' => 'Edit Test Opportunity',
        'stage' => 'lead',
        'assigned_to' => $this->user->id,
    ]);

    loginAndVisitOpportunities($this);

    $page = visit('/app/'.$this->team->slug.'/opportunities/'.$opportunity->id.'/edit');

    $page->assertSee('Edit Opportunity')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('opportunities/edit'));
});
