<?php

declare(strict_types=1);

use App\Enums\InteractionType;
use App\Enums\OpportunityStage;
use App\Listeners\ProcessFormSubmissionToCrm;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\FormCrmSetting;
use App\Models\Interaction;
use App\Models\LeadScore;
use App\Models\Opportunity;
use App\Models\Team;
use Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Madbox99\FilamentFormBuilder\Support\FormFieldBlueprint;
use Madbox99\FilamentFormBuilder\ValueObjects\SubmissionActions;

function leadForm(Team $team): RegistrationForm
{
    return RegistrationForm::factory()->create([
        'team_id' => $team->id,
        'fields' => [
            ['type' => FormFieldBlueprint::TYPE_TEXT, 'data' => ['label' => 'Name', 'name' => 'name', 'required' => true]],
            ['type' => FormFieldBlueprint::TYPE_EMAIL, 'data' => ['label' => 'Email', 'name' => 'email', 'required' => true]],
            ['type' => FormFieldBlueprint::TYPE_PHONE, 'data' => ['label' => 'Phone', 'name' => 'phone', 'required' => false]],
        ],
    ]);
}

/**
 * @param  array<string, mixed>  $data
 */
function fireSubmission(RegistrationForm $form, Team $team, array $data, ?SubmissionActions $actions = null): FormSubmission
{
    $submission = FormSubmission::factory()->create([
        'registration_form_id' => $form->id,
        'team_id' => $team->id,
        'data' => $data,
    ]);

    app(ProcessFormSubmissionToCrm::class)->handle(new FormSubmissionProcessed(
        $form,
        $submission,
        $data,
        $actions ?? new SubmissionActions(),
    ));

    return $submission->refresh();
}

it('creates customer, opportunity and interaction from a submission with email', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    $submission = fireSubmission($form, $team, ['name' => 'Kiss Anna', 'email' => 'anna@example.com', 'phone' => '+3630']);

    $customer = Customer::query()->where('team_id', $team->id)->where('email', 'anna@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('Kiss Anna')
        ->and($customer->phone)->toBe('+3630')
        ->and($submission->lead_id)->toBe($customer->id);

    expect(Opportunity::query()->where('customer_id', $customer->id)->where('stage', OpportunityStage::Lead)->count())->toBe(1);
    expect(Interaction::query()->where('customer_id', $customer->id)->where('type', InteractionType::FormSubmission)->count())->toBe(1);
});

it('deduplicates the customer by email but adds a new opportunity and interaction', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);
    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    expect(Customer::query()->where('team_id', $team->id)->where('email', 'anna@example.com')->count())->toBe(1);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->count())->toBe(2);
    expect(Interaction::query()->where('customer_id', $customer->id)->count())->toBe(2);
});

it('skips crm records when there is no email', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    $submission = fireSubmission($form, $team, ['name' => 'No Email']);

    expect(Customer::query()->where('team_id', $team->id)->count())->toBe(0)
        ->and($submission->lead_id)->toBeNull();
});

it('respects createLeadIfHasEmail = false', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com'], new SubmissionActions(createLeadIfHasEmail: false));

    expect(Customer::query()->where('team_id', $team->id)->count())->toBe(0);
});

it('never mixes data across teams in queue context', function (): void {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();
    Customer::factory()->create(['team_id' => $teamB->id, 'email' => 'anna@example.com']);
    $form = leadForm($teamA);

    fireSubmission($form, $teamA, ['name' => 'Anna', 'email' => 'anna@example.com']);

    expect(Customer::query()->where('team_id', $teamA->id)->where('email', 'anna@example.com')->count())->toBe(1)
        ->and(Customer::query()->where('team_id', $teamB->id)->where('email', 'anna@example.com')->count())->toBe(1);
});

it('links the opportunity to a fixed campaign from settings', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);
    $campaign = Campaign::factory()->create(['team_id' => $team->id]);
    FormCrmSetting::query()->create([
        'team_id' => $team->id,
        'registration_form_id' => $form->id,
        'campaign_id' => $campaign->id,
    ]);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->value('campaign_id'))->toBe($campaign->id);
});

it('links the opportunity to a campaign matched by utm_campaign', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);
    $campaign = Campaign::factory()->create(['team_id' => $team->id, 'name' => 'Summer']);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com', 'utm_campaign' => 'summer']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(Opportunity::query()->where('customer_id', $customer->id)->value('campaign_id'))->toBe($campaign->id);
});

it('recalculates the lead score for the customer', function (): void {
    $team = Team::factory()->create();
    $form = leadForm($team);

    fireSubmission($form, $team, ['name' => 'Anna', 'email' => 'anna@example.com']);

    $customer = Customer::query()->where('email', 'anna@example.com')->first();
    expect(LeadScore::query()->where('team_id', $team->id)->where('customer_id', $customer->id)->exists())->toBeTrue();
});
