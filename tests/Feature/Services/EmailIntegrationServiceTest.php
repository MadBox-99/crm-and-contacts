<?php

declare(strict_types=1);

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\Team;
use App\Services\EmailIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = resolve(EmailIntegrationService::class);
    $this->team = Team::factory()->create();
    $this->customer = Customer::factory()->for($this->team)->create(['email' => 'customer@example.com']);
});

it('sends an outbound email and creates communication record', function (): void {
    Mail::fake();

    $communication = $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Test Subject',
        body: 'Test body content',
        customerId: $this->customer->id,
    );

    expect($communication)->toBeInstanceOf(Communication::class)
        ->and($communication->team_id)->toBe($this->team->id)
        ->and($communication->customer_id)->toBe($this->customer->id)
        ->and($communication->channel)->toBe(CommunicationChannel::Email)
        ->and($communication->direction)->toBe(CommunicationDirection::Outbound)
        ->and($communication->status)->toBe(CommunicationStatus::Sent)
        ->and($communication->subject)->toBe('Test Subject')
        ->and($communication->content)->toBe('Test body content')
        ->and($communication->message_id)->not->toBeNull()
        ->and($communication->thread_id)->not->toBeNull();
});

it('creates a reply in the same thread', function (): void {
    Mail::fake();

    $original = $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Original Subject',
        body: 'First message',
        customerId: $this->customer->id,
    );

    $reply = $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Re: Original Subject',
        body: 'Reply message',
        customerId: $this->customer->id,
        replyToMessageId: $original->message_id,
    );

    expect($reply->thread_id)->toBe($original->thread_id)
        ->and($reply->in_reply_to)->toBe($original->message_id);
});

it('retrieves a full email thread', function (): void {
    Mail::fake();

    $msg1 = $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Thread Subject',
        body: 'Message 1',
        customerId: $this->customer->id,
    );

    $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Re: Thread Subject',
        body: 'Message 2',
        customerId: $this->customer->id,
        replyToMessageId: $msg1->message_id,
    );

    $thread = $this->service->getThread($msg1->thread_id, $this->team->id);

    expect($thread)->toHaveCount(2);
});

it('retrieves customer email threads', function (): void {
    Mail::fake();

    // Create two separate threads
    $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Thread A',
        body: 'Body A',
        customerId: $this->customer->id,
    );

    $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'customer@example.com',
        subject: 'Thread B',
        body: 'Body B',
        customerId: $this->customer->id,
    );

    $threads = $this->service->getCustomerThreads($this->customer->id, $this->team->id);

    expect($threads)->toHaveCount(2);
});

it('does not mix threads from different teams', function (): void {
    Mail::fake();

    $otherTeam = Team::factory()->create();

    $msg = $this->service->sendEmail(
        teamId: $this->team->id,
        toEmail: 'test@example.com',
        subject: 'Team A message',
        body: 'Body',
    );

    $this->service->sendEmail(
        teamId: $otherTeam->id,
        toEmail: 'test@example.com',
        subject: 'Team B message',
        body: 'Body',
    );

    $thread = $this->service->getThread($msg->thread_id, $this->team->id);

    expect($thread)->toHaveCount(1);
});
