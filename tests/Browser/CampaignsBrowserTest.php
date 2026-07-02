<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'campaigns@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitCampaigns(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'campaigns@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/campaigns');
}

it('renders the campaigns list page', function (): void {
    $page = loginAndVisitCampaigns($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/campaigns')
        ->assertSee('Campaigns')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('campaigns/list'), fullPage: true);
});

it('displays seeded campaigns in the table', function (): void {
    Campaign::factory()->for($this->team)->create([
        'name' => 'Browser Test Campaign',
        'created_by' => $this->user->id,
    ]);

    $page = loginAndVisitCampaigns($this);

    $page->assertSee('Browser Test Campaign')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('campaigns/with-data'), fullPage: true);
});

it('renders the campaign view page', function (): void {
    $campaign = Campaign::factory()->for($this->team)->create([
        'name' => 'View Test Campaign',
        'created_by' => $this->user->id,
        'target_audience_criteria' => null,
    ]);

    loginAndVisitCampaigns($this);

    $page = visit('/app/'.$this->team->slug.'/campaigns/'.$campaign->id);

    $page->assertSee('View Test Campaign')
        ->assertSee('Campaign details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('campaigns/view'));
});
