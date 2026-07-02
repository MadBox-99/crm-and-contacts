<?php

declare(strict_types=1);

use App\Enums\InteractionCategory;
use App\Enums\InteractionChannel;
use App\Enums\InteractionDirection;
use App\Enums\InteractionStatus;
use App\Enums\InteractionType;
use App\Mail\TemplateEmail;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\Interaction;
use App\Models\Team;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create();
    $this->user->teams()->attach($this->team);
    $this->actingAs($this->user);

    $this->service = resolve(EmailService::class);
});

it('sends an email and creates an interaction record', function (): void {
    Mail::fake();

    $customer = Customer::factory()->for($this->team)->create();
    $template = EmailTemplate::factory()->sales()->create([
        'subject' => 'Hello {contact_name}',
        'body' => '<p>Dear {contact_name},</p>',
    ]);

    $interaction = $this->service->send(
        template: $template,
        recipientEmail: 'john@example.com',
        recipientName: 'John Doe',
        context: ['customer' => $customer],
    );

    Mail::assertQueued(TemplateEmail::class);

    expect($interaction)->toBeInstanceOf(Interaction::class)
        ->and($interaction->type)->toBe(InteractionType::Email)
        ->and($interaction->channel)->toBe(InteractionChannel::Email)
        ->and($interaction->direction)->toBe(InteractionDirection::Outbound)
        ->and($interaction->status)->toBe(InteractionStatus::Completed)
        ->and($interaction->customer_id)->toBe($customer->id)
        ->and($interaction->email_recipient)->toBe('john@example.com');
});

it('maps sales template category to sales interaction category', function (): void {
    Mail::fake();

    $customer = Customer::factory()->for($this->team)->create();
    $template = EmailTemplate::factory()->sales()->create();

    $interaction = $this->service->send(
        template: $template,
        recipientEmail: 'test@example.com',
        recipientName: 'Test',
        context: ['customer' => $customer],
    );

    expect($interaction->category)->toBe(InteractionCategory::Sales);
});

it('maps marketing template category to marketing interaction category', function (): void {
    Mail::fake();

    $customer = Customer::factory()->for($this->team)->create();
    $template = EmailTemplate::factory()->marketing()->create();

    $interaction = $this->service->send(
        template: $template,
        recipientEmail: 'test@example.com',
        recipientName: 'Test',
        context: ['customer' => $customer],
    );

    expect($interaction->category)->toBe(InteractionCategory::Marketing);
});

it('stores email template id on the interaction', function (): void {
    Mail::fake();

    $customer = Customer::factory()->for($this->team)->create();
    $template = EmailTemplate::factory()->create();

    $interaction = $this->service->send(
        template: $template,
        recipientEmail: 'test@example.com',
        recipientName: 'Test',
        context: ['customer' => $customer],
    );

    expect($interaction->email_template_id)->toBe($template->id);
});

it('logs activity for the sent email', function (): void {
    Mail::fake();

    $customer = Customer::factory()->for($this->team)->create();
    $template = EmailTemplate::factory()->create(['name' => 'Welcome Email']);

    $interaction = $this->service->send(
        template: $template,
        recipientEmail: 'log@example.com',
        recipientName: 'Log Test',
        context: ['customer' => $customer],
    );

    $activity = Activity::query()
        ->where('subject_type', 'interaction')
        ->where('subject_id', $interaction->id)
        ->latest()
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->event)->toBe('email_sent')
        ->and($activity->properties['recipient'])->toBe('log@example.com')
        ->and($activity->properties['template_name'])->toBe('Welcome Email');
});
