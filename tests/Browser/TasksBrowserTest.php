<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create([
        'email' => 'tasks@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->user->teams()->attach($this->team);
});

function loginAndVisitTasks(object $context): mixed
{
    $page = visit('/app/login');

    $page->type('#form\\.email', 'tasks@example.com')
        ->type('#form\\.password', 'password')
        ->submit()
        ->wait(2);

    return visit('/app/'.$context->team->slug.'/tasks');
}

it('renders the tasks list page', function (): void {
    $page = loginAndVisitTasks($this);

    $page->assertPathIs('/app/'.$this->team->slug.'/tasks')
        ->assertSee('Tasks')
        ->assertSee('New Task')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('tasks/list'), fullPage: true);
});

it('displays seeded tasks in the table', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    Task::factory()->for($customer)->for($this->team)->create([
        'title' => 'Browser Test Task',
        'assigned_to' => $this->user->id,
        'assigned_by' => $this->user->id,
    ]);

    $page = loginAndVisitTasks($this);

    $page->assertSee('Browser Test Task')
        ->assertNoJavaScriptErrors()
        ->screenshot(filename: screenshotPath('tasks/with-data'), fullPage: true);
});

it('renders the task view page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $task = Task::factory()->for($customer)->for($this->team)->create([
        'title' => 'View Test Task',
        'assigned_to' => $this->user->id,
        'assigned_by' => $this->user->id,
    ]);

    loginAndVisitTasks($this);

    $page = visit('/app/'.$this->team->slug.'/tasks/'.$task->id);

    $page->assertSee('View Test Task')
        ->assertSee('Task details')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('tasks/view'));
});

it('renders the task edit page', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    $task = Task::factory()->for($customer)->for($this->team)->create([
        'assigned_to' => $this->user->id,
        'assigned_by' => $this->user->id,
    ]);

    loginAndVisitTasks($this);

    $page = visit('/app/'.$this->team->slug.'/tasks/'.$task->id.'/edit');

    $page->assertSee('Edit Task')
        ->assertNoJavaScriptErrors()
        ->screenshot(fullPage: true, filename: screenshotPath('tasks/edit'));
});
