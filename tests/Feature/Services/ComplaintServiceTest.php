<?php

declare(strict_types=1);

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintType;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\User;
use App\Services\ComplaintService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(ComplaintService::class);
    $this->customer = Customer::factory()->create();
});

it('creates complaint with auto-generated number', function (): void {
    $complaint = $this->service->createComplaint($this->customer, [
        'subject' => 'Test complaint',
        'description' => 'Description of the complaint',
        'severity' => ComplaintSeverity::Medium,
        'type' => ComplaintType::Product,
    ]);

    expect($complaint->complaint_number)->toStartWith('CMP-')
        ->and($complaint->customer_id)->toBe($this->customer->id)
        ->and($complaint->status)->toBe(ComplaintStatus::Open)
        ->and($complaint->type)->toBe(ComplaintType::Product)
        ->and($complaint->sla_deadline_at)->not->toBeNull();
});

it('sets SLA deadline based on severity', function (ComplaintSeverity $severity, int $expectedHours): void {
    $complaint = $this->service->createComplaint($this->customer, [
        'description' => 'Test',
        'severity' => $severity,
    ]);

    $hoursDiff = (int) round($complaint->reported_at->diffInHours($complaint->sla_deadline_at));

    expect($hoursDiff)->toBe($expectedHours);
})->with([
    'Critical = 4h' => [ComplaintSeverity::Critical, 4],
    'High = 24h' => [ComplaintSeverity::High, 24],
    'Medium = 72h' => [ComplaintSeverity::Medium, 72],
    'Low = 168h' => [ComplaintSeverity::Low, 168],
]);

it('escalates complaint', function (): void {
    $complaint = Complaint::factory()->create([
        'status' => ComplaintStatus::Open,
        'escalation_level' => 0,
    ]);

    $escalatedTo = User::factory()->create();
    $escalatedBy = User::factory()->create();

    $escalation = $this->service->escalate($complaint, $escalatedTo, $escalatedBy, 'Needs senior attention');

    $complaint->refresh();

    expect($escalation->complaint_id)->toBe($complaint->id)
        ->and($escalation->escalated_to)->toBe($escalatedTo->id)
        ->and($complaint->assigned_to)->toBe($escalatedTo->id)
        ->and($complaint->escalation_level)->toBe(1)
        ->and($complaint->status)->toBe(ComplaintStatus::InProgress);
});

it('resolves complaint', function (): void {
    $complaint = Complaint::factory()->create([
        'status' => ComplaintStatus::InProgress,
    ]);

    $resolved = $this->service->resolve($complaint, 'Issue was fixed by replacing the product.');

    expect($resolved->status)->toBe(ComplaintStatus::Resolved)
        ->and($resolved->resolution)->toBe('Issue was fixed by replacing the product.')
        ->and($resolved->resolved_at)->not->toBeNull();
});

it('finds overdue SLA complaints', function (): void {
    // Create overdue complaint
    Complaint::factory()->create([
        'status' => ComplaintStatus::Open,
        'sla_deadline_at' => now()->subHour(),
    ]);

    // Create non-overdue complaint
    Complaint::factory()->create([
        'status' => ComplaintStatus::Open,
        'sla_deadline_at' => now()->addDay(),
    ]);

    // Create resolved complaint (should not appear even if overdue)
    Complaint::factory()->create([
        'status' => ComplaintStatus::Resolved,
        'sla_deadline_at' => now()->subHour(),
    ]);

    $overdue = $this->service->getOverdueSlaComplaints();

    expect($overdue)->toHaveCount(1);
});

it('returns statistics by type', function (): void {
    Complaint::factory()->count(3)->create(['type' => ComplaintType::Product]);
    Complaint::factory()->count(2)->create(['type' => ComplaintType::Billing]);
    Complaint::factory()->create(['type' => ComplaintType::Delivery]);

    $stats = $this->service->getStatisticsByType();

    expect($stats)->toHaveKey('product')
        ->and($stats['product'])->toBe(3)
        ->and($stats['billing'])->toBe(2)
        ->and($stats['delivery'])->toBe(1);
});
