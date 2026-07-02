<?php

declare(strict_types=1);

use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'complaints@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitComplaints(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'complaints@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/complaints');
}

it('renders the complaints list page', function (): void {
    $page = loginAndVisitComplaints($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/complaints')
        ->assertSee('Complaints')
        ->assertSee('New Complaint')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('complaints/list'), fullPage: true);
});

it('displays seeded complaints in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create([
        'name' => 'Complaining Customer',
    ]);

    Complaint::factory()->for($customer)->for($this->team)->create([
        'subject' => 'Browser Test Complaint',
        'reported_by' => $this->user->id,
        'assigned_to' => $this->user->id,
        'order_id' => null,
    ]);

    $page = loginAndVisitComplaints($this);

    $page->assertSee('Complaining Customer')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('complaints/with-data'), fullPage: true);
});

it('renders the complaint view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $complaint = Complaint::factory()->for($customer)->for($this->team)->create([
        'title' => 'View Test Complaint',
        'reported_by' => $this->user->id,
        'assigned_to' => $this->user->id,
        'order_id' => null,
    ]);

    loginAndVisitComplaints($this);

    $page = visit('/app/'.$this->team->slug.'/complaints/'.$complaint->id);

    $page->assertSee('Complaint details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('complaints/view'));
});

it('renders the complaint edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $complaint = Complaint::factory()->for($customer)->for($this->team)->create([
        'reported_by' => $this->user->id,
        'assigned_to' => $this->user->id,
        'order_id' => null,
    ]);

    loginAndVisitComplaints($this);

    $page = visit('/app/'.$this->team->slug.'/complaints/'.$complaint->id.'/edit');

    $page->assertSee('Edit Complaint')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('complaints/edit'));
});
