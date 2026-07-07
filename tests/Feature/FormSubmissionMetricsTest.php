<?php

declare(strict_types=1);

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;

it('aggregates submission stats scoped to the team', function (): void {
    $team = Team::factory()->create();
    $other = Team::factory()->create();
    $form = RegistrationForm::factory()->create(['team_id' => $team->id]);

    FormSubmission::factory()->count(3)->create(['registration_form_id' => $form->id, 'team_id' => $team->id, 'lead_id' => null]);
    FormSubmission::factory()->create(['registration_form_id' => $form->id, 'team_id' => $team->id, 'lead_id' => 999]);
    FormSubmission::factory()->create(['registration_form_id' => $form->id, 'team_id' => $other->id]);

    $stats = (new FormSubmissionMetricsService())->stats($team->id);

    expect($stats['total'])->toBe(4)
        ->and($stats['converted'])->toBe(1)
        ->and($stats['conversion_rate'])->toBe(25.0);
});

it('builds a per-form breakdown', function (): void {
    $team = Team::factory()->create();
    $formA = RegistrationForm::factory()->create(['team_id' => $team->id, 'name' => 'Alpha']);
    $formB = RegistrationForm::factory()->create(['team_id' => $team->id, 'name' => 'Beta']);
    FormSubmission::factory()->count(2)->create(['registration_form_id' => $formA->id, 'team_id' => $team->id]);
    FormSubmission::factory()->create(['registration_form_id' => $formB->id, 'team_id' => $team->id]);

    $byForm = (new FormSubmissionMetricsService())->byForm($team->id);

    expect($byForm['labels'])->toContain('Alpha', 'Beta')
        ->and(array_sum($byForm['values']))->toBe(3);
});
