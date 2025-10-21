<?php

declare(strict_types=1);

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintEscalation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $complaint = Complaint::factory()->create();

    expect($complaint)->toBeInstanceOf(Complaint::class)
        ->and($complaint->title)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);
    $reporter = User::factory()->create();
    $assignedUser = User::factory()->create();

    $complaint = Complaint::factory()->create([
        'customer_id' => $customer->id,
        'order_id' => $order->id,
        'reported_by' => $reporter->id,
        'assigned_to' => $assignedUser->id,
        'title' => 'Test Complaint',
        'description' => 'Test Description',
        'severity' => ComplaintSeverity::High,
        'status' => ComplaintStatus::Open,
        'resolution' => null,
        'reported_at' => now(),
        'resolved_at' => null,
    ]);

    expect($complaint->customer_id)->toBe($customer->id)
        ->and($complaint->order_id)->toBe($order->id)
        ->and($complaint->reported_by)->toBe($reporter->id)
        ->and($complaint->assigned_to)->toBe($assignedUser->id)
        ->and($complaint->title)->toBe('Test Complaint')
        ->and($complaint->description)->toBe('Test Description')
        ->and($complaint->severity)->toBe(ComplaintSeverity::High)
        ->and($complaint->status)->toBe(ComplaintStatus::Open)
        ->and($complaint->resolution)->toBeNull()
        ->and($complaint->resolved_at)->toBeNull();
});

it('casts reported_at to datetime', function (): void {
    $complaint = Complaint::factory()->create(['reported_at' => now()]);

    expect($complaint->reported_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts resolved_at to datetime', function (): void {
    $complaint = Complaint::factory()->create(['resolved_at' => now()]);

    expect($complaint->resolved_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $complaint = Complaint::factory()->create(['customer_id' => $customer->id]);

    expect($complaint->customer)->toBeInstanceOf(Customer::class)
        ->and($complaint->customer->id)->toBe($customer->id);
});

it('has order relationship', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);
    $complaint = Complaint::factory()->create([
        'customer_id' => $customer->id,
        'order_id' => $order->id,
    ]);

    expect($complaint->order)->toBeInstanceOf(Order::class)
        ->and($complaint->order->id)->toBe($order->id);
});

it('has reporter relationship', function (): void {
    $user = User::factory()->create();
    $complaint = Complaint::factory()->create(['reported_by' => $user->id]);

    expect($complaint->reporter)->toBeInstanceOf(User::class)
        ->and($complaint->reporter->id)->toBe($user->id);
});

it('has assignedUser relationship', function (): void {
    $user = User::factory()->create();
    $complaint = Complaint::factory()->create(['assigned_to' => $user->id]);

    expect($complaint->assignedUser)->toBeInstanceOf(User::class)
        ->and($complaint->assignedUser->id)->toBe($user->id);
});

it('has escalations relationship', function (): void {
    $complaint = Complaint::factory()->create();
    $escalation = ComplaintEscalation::factory()->create(['complaint_id' => $complaint->id]);

    expect($complaint->escalations)->toHaveCount(1)
        ->and($complaint->escalations->first()->id)->toBe($escalation->id);
});
