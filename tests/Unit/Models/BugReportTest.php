<?php

declare(strict_types=1);

use App\Enums\BugReportStatus;
use App\Enums\ComplaintSeverity;
use App\Models\BugReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $bugReport = BugReport::factory()->create();

    expect($bugReport)->toBeInstanceOf(BugReport::class)
        ->and($bugReport->title)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $user = User::factory()->create();
    $assignedUser = User::factory()->create();

    $bugReport = BugReport::factory()->create([
        'user_id' => $user->id,
        'title' => 'Bug Title',
        'description' => 'Bug Description',
        'severity' => ComplaintSeverity::High,
        'status' => BugReportStatus::Open,
        'source' => 'Unit Test',
        'assigned_to' => $assignedUser->id,
        'resolved_at' => null,
    ]);

    expect($bugReport->user_id)->toBe($user->id)
        ->and($bugReport->title)->toBe('Bug Title')
        ->and($bugReport->description)->toBe('Bug Description')
        ->and($bugReport->severity)->toBe(ComplaintSeverity::High)
        ->and($bugReport->status)->toBe(BugReportStatus::Open)
        ->and($bugReport->source)->toBe('Unit Test')
        ->and($bugReport->assigned_to)->toBe($assignedUser->id)
        ->and($bugReport->resolved_at)->toBeNull();
});

it('casts resolved_at to datetime', function (): void {
    $bugReport = BugReport::factory()->create(['resolved_at' => now()]);

    expect($bugReport->resolved_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has user relationship', function (): void {
    $user = User::factory()->create();
    $bugReport = BugReport::factory()->create(['user_id' => $user->id]);

    expect($bugReport->user)->toBeInstanceOf(User::class)
        ->and($bugReport->user->id)->toBe($user->id);
});

it('has assignedUser relationship', function (): void {
    $user = User::factory()->create();
    $bugReport = BugReport::factory()->create(['assigned_to' => $user->id]);

    expect($bugReport->assignedUser)->toBeInstanceOf(User::class)
        ->and($bugReport->assignedUser->id)->toBe($user->id);
});
