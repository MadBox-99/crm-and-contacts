<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\InteractionType;
use App\Enums\OpportunityStage;
use App\Mail\NewFormSubmissionMail;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\FormCrmSetting;
use App\Models\Interaction;
use App\Models\Opportunity;
use App\Models\Team;
use App\Services\LeadScoringService;
use App\Services\SubmissionData;
use App\Services\SubmissionFieldMapper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Madbox99\FilamentFormBuilder\Events\FormSubmissionProcessed;
use Madbox99\FilamentFormBuilder\Models\FormSubmission;
use Madbox99\FilamentFormBuilder\Models\RegistrationForm;
use Madbox99\FilamentFormBuilder\ValueObjects\SubmissionActions;

final class ProcessFormSubmissionToCrm implements ShouldQueue
{
    public function __construct(
        private readonly SubmissionFieldMapper $mapper,
        private readonly LeadScoringService $scoring,
    ) {}

    public function handle(FormSubmissionProcessed $event): void
    {
        $form = $event->form;
        $submission = $event->submission;
        $actions = $event->actions;

        $teamId = $submission?->team_id ?? $form->getAttribute('team_id');

        $settings = $teamId !== null
            ? FormCrmSetting::query()
                ->where('team_id', $teamId)
                ->where('registration_form_id', $form->id)
                ->first()
            : null;

        $mapped = $this->mapper->map($form, $event->formData, $settings);

        if ($submission === null || $teamId === null || ! $mapped->hasEmail() || ! $actions->createLeadIfHasEmail) {
            $this->notify($actions, $form, $submission, $mapped, null);

            return;
        }

        $customer = DB::transaction(function () use ($teamId, $form, $submission, $mapped, $settings): Customer {
            $customer = $this->resolveCustomer($teamId, $mapped);

            if ($settings?->create_opportunity ?? true) {
                $this->createOpportunity($teamId, $customer, $form, $submission, $mapped, $settings);
            }

            $this->logInteraction($teamId, $customer, $form, $submission, $mapped);

            $submission->update(['lead_id' => $customer->id]);

            return $customer;
        });

        if (($settings?->enable_scoring ?? true) && ($team = Team::query()->find($teamId)) instanceof Team) {
            $this->scoring->calculateForCustomer($customer, $team);
        }

        $this->notify($actions, $form, $submission, $mapped, $customer);
    }

    private function resolveCustomer(int $teamId, SubmissionData $data): Customer
    {
        $customer = Customer::query()
            ->where('team_id', $teamId)
            ->where('email', $data->email)
            ->first();

        if (! $customer instanceof Customer) {
            return Customer::query()->create([
                'team_id' => $teamId,
                'email' => $data->email,
                'name' => $data->companyName ?? $data->name ?? $data->email,
                'phone' => $data->phone,
                'is_active' => true,
            ]);
        }

        if (($customer->phone === null || $customer->phone === '') && $data->phone !== null) {
            $customer->update(['phone' => $data->phone]);
        }

        return $customer;
    }

    private function createOpportunity(int $teamId, Customer $customer, RegistrationForm $form, FormSubmission $submission, SubmissionData $data, ?FormCrmSetting $settings): void
    {
        $stage = $settings?->opportunity_stage !== null
            ? (OpportunityStage::tryFrom($settings->opportunity_stage) ?? OpportunityStage::Lead)
            : OpportunityStage::Lead;

        Opportunity::query()->create([
            'team_id' => $teamId,
            'customer_id' => $customer->id,
            'campaign_id' => $this->resolveCampaignId($teamId, $data, $settings),
            'title' => $form->name.' – '.$submission->created_at->format('Y-m-d H:i'),
            'description' => $this->descriptionWithUtm($data),
            'stage' => $stage,
        ]);
    }

    private function logInteraction(int $teamId, Customer $customer, RegistrationForm $form, FormSubmission $submission, SubmissionData $data): void
    {
        Interaction::query()->create([
            'team_id' => $teamId,
            'customer_id' => $customer->id,
            'user_id' => null,
            'type' => InteractionType::FormSubmission,
            'subject' => $form->name,
            'description' => $this->descriptionWithUtm($data),
            'interaction_date' => $submission->created_at,
        ]);
    }

    private function resolveCampaignId(int $teamId, SubmissionData $data, ?FormCrmSetting $settings): ?int
    {
        if ($settings?->campaign_id !== null) {
            return $settings->campaign_id;
        }

        $utmCampaign = $data->utm['utm_campaign'] ?? null;
        if ($utmCampaign === null || $utmCampaign === '') {
            return null;
        }

        $id = Campaign::query()
            ->where('team_id', $teamId)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($utmCampaign)])
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function descriptionWithUtm(SubmissionData $data): string
    {
        $description = $data->notes;

        if ($data->utm !== []) {
            $utm = collect($data->utm)
                ->map(static fn (string $value, string $key): string => $key.': '.$value)
                ->implode(', ');
            $description .= "\n\nUTM: ".$utm;
        }

        if ($data->referrer !== null) {
            $description .= "\nReferrer: ".$data->referrer;
        }

        return $description;
    }

    private function notify(SubmissionActions $actions, RegistrationForm $form, ?FormSubmission $submission, SubmissionData $data, ?Customer $customer): void
    {
        if ($actions->notifyEmails === [] || $submission === null) {
            return;
        }

        Mail::to($actions->notifyEmails)->send(new NewFormSubmissionMail($form, $submission, $data, $customer));
    }
}
