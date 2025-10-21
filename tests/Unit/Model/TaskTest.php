<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $task = Task::factory()->create();

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $assignedUser = User::factory()->create();
    $assigner = User::factory()->create();

    $task = Task::factory()->create([
        'customer_id' => $customer->id,
        'assigned_to' => $assignedUser->id,
        'assigned_by' => $assigner->id,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'priority' => 'high',
        'status' => 'pending',
        'due_date' => now()->addDays(7),
        'completed_at' => null,
    ]);

    expect($task->customer_id)->toBe($customer->id)
        ->and($task->assigned_to)->toBe($assignedUser->id)
        ->and($task->assigned_by)->toBe($assigner->id)
        ->and($task->title)->toBe('Test Task')
        ->and($task->description)->toBe('Test Description')
        ->and($task->priority)->toBe('high')
        ->and($task->status)->toBe('pending')
        ->and($task->completed_at)->toBeNull();
});

it('casts due_date to date', function (): void {
    $task = Task::factory()->create(['due_date' => '2025-02-15']);

    expect($task->due_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts completed_at to datetime', function (): void {
    $task = Task::factory()->create(['completed_at' => now()]);

    expect($task->completed_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $task = Task::factory()->create(['customer_id' => $customer->id]);

    expect($task->customer)->toBeInstanceOf(Customer::class)
        ->and($task->customer->id)->toBe($customer->id);
});

it('has assignedUser relationship', function (): void {
    $user = User::factory()->create();
    $task = Task::factory()->create(['assigned_to' => $user->id]);

    expect($task->assignedUser)->toBeInstanceOf(User::class)
        ->and($task->assignedUser->id)->toBe($user->id);
});

it('has assigner relationship', function (): void {
    $user = User::factory()->create();
    $task = Task::factory()->create(['assigned_by' => $user->id]);

    expect($task->assigner)->toBeInstanceOf(User::class)
        ->and($task->assigner->id)->toBe($user->id);
});
