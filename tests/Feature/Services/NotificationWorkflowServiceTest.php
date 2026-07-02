<?php

declare(strict_types=1);

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\OpportunityStage;
use App\Enums\QuoteStatus;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\Opportunity;
use App\Models\Quote;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Notifications\ComplaintSlaBreachNotification;
use App\Notifications\LeadInactiveNotification;
use App\Notifications\QuoteExpiringNotification;
use App\Notifications\TaskDeadlineNotification;
use App\Services\NotificationWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(NotificationWorkflowService::class);
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create();
    $this->user->teams()->attach($this->team);
    $this->customer = Customer::factory()->for($this->team)->create();
});

it('notifies about expiring quotes', function (): void {
    Notification::fake();

    Quote::factory()->for($this->customer)->for($this->team)->create([
        'status' => QuoteStatus::Sent,
        'valid_until' => now()->addDays(2),
    ]);

    $count = $this->service->checkExpiringQuotes(3);

    expect($count)->toBe(1);

    Notification::assertSentTo($this->user, QuoteExpiringNotification::class);
});

it('does not notify about non-expiring quotes', function (): void {
    Notification::fake();

    Quote::factory()->for($this->customer)->for($this->team)->create([
        'status' => QuoteStatus::Sent,
        'valid_until' => now()->addDays(10),
    ]);

    $count = $this->service->checkExpiringQuotes(3);

    expect($count)->toBe(0);

    Notification::assertNothingSent();
});

it('notifies about approaching task deadlines', function (): void {
    Notification::fake();

    Task::factory()->for($this->team)->create([
        'assigned_to' => $this->user->id,
        'due_date' => now()->addDay(),
        'completed_at' => null,
    ]);

    $count = $this->service->checkApproachingDeadlines(2);

    expect($count)->toBe(1);

    Notification::assertSentTo($this->user, TaskDeadlineNotification::class);
});

it('does not notify about completed tasks', function (): void {
    Notification::fake();

    Task::factory()->for($this->team)->create([
        'assigned_to' => $this->user->id,
        'due_date' => now()->addDay(),
        'completed_at' => now(),
    ]);

    $count = $this->service->checkApproachingDeadlines(2);

    expect($count)->toBe(0);

    Notification::assertNotSentTo($this->user, TaskDeadlineNotification::class);
});

it('notifies about inactive leads', function (): void {
    Notification::fake();

    Opportunity::factory()->for($this->customer)->for($this->team)->create([
        'stage' => OpportunityStage::Lead,
    ]);

    // Old interaction (10 days ago)
    Interaction::factory()->for($this->customer)->for($this->team)->create([
        'interaction_date' => now()->subDays(10),
    ]);

    $count = $this->service->checkInactiveLeads(7);

    expect($count)->toBe(1);

    Notification::assertSentTo($this->user, LeadInactiveNotification::class);
});

it('does not notify about recently active leads', function (): void {
    Notification::fake();

    Opportunity::factory()->for($this->customer)->for($this->team)->create([
        'stage' => OpportunityStage::Lead,
    ]);

    // Recent interaction
    Interaction::factory()->for($this->customer)->for($this->team)->create([
        'interaction_date' => now()->subDays(2),
    ]);

    $count = $this->service->checkInactiveLeads(7);

    expect($count)->toBe(0);

    Notification::assertNothingSent();
});

it('notifies about SLA breaches', function (): void {
    Notification::fake();

    Complaint::factory()->for($this->customer)->for($this->team)->create([
        'status' => ComplaintStatus::Open,
        'severity' => ComplaintSeverity::High,
        'sla_deadline_at' => now()->subHours(2),
        'assigned_to' => $this->user->id,
    ]);

    $count = $this->service->checkSlaBreaches();

    expect($count)->toBe(1);

    Notification::assertSentTo($this->user, ComplaintSlaBreachNotification::class);
});

it('does not notify about resolved complaints', function (): void {
    Notification::fake();

    Complaint::factory()->for($this->customer)->for($this->team)->create([
        'status' => ComplaintStatus::Resolved,
        'severity' => ComplaintSeverity::High,
        'sla_deadline_at' => now()->subHours(2),
    ]);

    $count = $this->service->checkSlaBreaches();

    expect($count)->toBe(0);

    Notification::assertNothingSent();
});

it('runs all checks at once', function (): void {
    Notification::fake();

    $results = $this->service->runAll();

    expect($results)->toHaveKeys(['quotes', 'tasks', 'leads', 'complaints']);
});
