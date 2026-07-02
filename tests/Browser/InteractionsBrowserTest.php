<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Interaction;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'interactions@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitInteractions(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'interactions@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/interactions');
}

it('renders the interactions list page', function (): void {
    $page = loginAndVisitInteractions($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/interactions')
        ->assertSee('Interactions')
        ->assertSee('New Interaction')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('interactions/list'), fullPage: true);
});

it('displays seeded interactions in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'Interaction Customer',
    ]);

    Interaction::factory()->for($customer)->for($this->team)->create([
        'subject' => 'Browser Test Call',
        'user_id' => $this->user->id,
    ]);

    $page = loginAndVisitInteractions($this);

    $page->assertSee('Browser Test Call')
        ->assertSee('Interaction Customer')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('interactions/with-data'), fullPage: true);
});

it('renders the interaction view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'View Interaction Customer',
    ]);

    $interaction = Interaction::factory()->for($customer)->for($this->team)->create([
        'subject' => 'View Test Interaction',
        'user_id' => $this->user->id,
    ]);

    loginAndVisitInteractions($this);

    $page = visit('/app/'.$this->team->slug.'/interactions/'.$interaction->id);

    $page->assertSee('Interaction Details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('interactions/view'));
});

it('renders the interaction edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $interaction = Interaction::factory()->for($customer)->for($this->team)->create([
        'user_id' => $this->user->id,
    ]);

    loginAndVisitInteractions($this);

    $page = visit('/app/'.$this->team->slug.'/interactions/'.$interaction->id.'/edit');

    $page->assertSee('Edit Interaction')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('interactions/edit'));
});
