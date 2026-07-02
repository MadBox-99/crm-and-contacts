<?php

declare(strict_types=1);

use App\Models\QuoteTemplate;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'quote-templates@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitQuoteTemplates(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'quote-templates@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/quote-templates');
}

it('renders the quote templates list page', function (): void {
    $page = loginAndVisitQuoteTemplates($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/quote-templates')
        ->assertSee('Quote Templates')
        ->assertSee('New Quote Template')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('quote-templates/list'), fullPage: true);
});

it('displays seeded quote templates in the table', function (): void {
    QuoteTemplate::factory()->for($this->team)->create([
        'name' => 'Browser Test Template',
        'created_by' => $this->user->id,
    ]);

    $page = loginAndVisitQuoteTemplates($this);

    $page->assertSee('Browser Test Template')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('quote-templates/with-data'), fullPage: true);
});

it('renders the quote template edit page', function (): void {
    $template = QuoteTemplate::factory()->for($this->team)->create([
        'created_by' => $this->user->id,
    ]);

    loginAndVisitQuoteTemplates($this);

    $page = visit('/app/'.$this->team->slug.'/quote-templates/'.$template->id.'/edit');

    $page->assertSee('Edit Quote Template')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('quote-templates/edit'));
});
