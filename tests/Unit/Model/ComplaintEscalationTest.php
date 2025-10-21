<?php

declare(strict_types=1);

use App\Models\Complaint;
use App\Models\ComplaintEscalation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $escalation = ComplaintEscalation::factory()->create();

    expect($escalation)->toBeInstanceOf(ComplaintEscalation::class);
});

it('has fillable attributes', function (): void {
    $complaint = Complaint::factory()->create();
    $escalatedTo = User::factory()->create();
    $escalatedBy = User::factory()->create();

    $escalation = ComplaintEscalation::factory()->create([
        'complaint_id' => $complaint->id,
        'escalated_to' => $escalatedTo->id,
        'escalated_by' => $escalatedBy->id,
        'reason' => 'High priority issue',
        'escalated_at' => now(),
    ]);

    expect($escalation->complaint_id)->toBe($complaint->id)
        ->and($escalation->escalated_to)->toBe($escalatedTo->id)
        ->and($escalation->escalated_by)->toBe($escalatedBy->id)
        ->and($escalation->reason)->toBe('High priority issue');
});

it('casts escalated_at to datetime', function (): void {
    $escalation = ComplaintEscalation::factory()->create(['escalated_at' => now()]);

    expect($escalation->escalated_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has complaint relationship', function (): void {
    $complaint = Complaint::factory()->create();
    $escalation = ComplaintEscalation::factory()->create(['complaint_id' => $complaint->id]);

    expect($escalation->complaint)->toBeInstanceOf(Complaint::class)
        ->and($escalation->complaint->id)->toBe($complaint->id);
});

it('has escalatedToUser relationship', function (): void {
    $user = User::factory()->create();
    $escalation = ComplaintEscalation::factory()->create(['escalated_to' => $user->id]);

    expect($escalation->escalatedToUser)->toBeInstanceOf(User::class)
        ->and($escalation->escalatedToUser->id)->toBe($user->id);
});

it('has escalatedByUser relationship', function (): void {
    $user = User::factory()->create();
    $escalation = ComplaintEscalation::factory()->create(['escalated_by' => $user->id]);

    expect($escalation->escalatedByUser)->toBeInstanceOf(User::class)
        ->and($escalation->escalatedByUser->id)->toBe($user->id);
});
