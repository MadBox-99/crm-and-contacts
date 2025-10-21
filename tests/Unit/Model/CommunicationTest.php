<?php

declare(strict_types=1);

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Models\Communication;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $communication = Communication::factory()->create();

    expect($communication)->toBeInstanceOf(Communication::class)
        ->and($communication->channel)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();

    $communication = Communication::factory()->create([
        'customer_id' => $customer->id,
        'channel' => 'email',
        'direction' => 'outbound',
        'subject' => 'Test Subject',
        'content' => 'Test Content',
        'status' => 'sent',
        'sent_at' => now(),
        'delivered_at' => now()->addMinutes(1),
        'read_at' => null,
    ]);

    expect($communication->customer_id)->toBe($customer->id)
        ->and($communication->channel)->toBe(CommunicationChannel::Email)
        ->and($communication->direction)->toBe(CommunicationDirection::Outbound)
        ->and($communication->subject)->toBe('Test Subject')
        ->and($communication->content)->toBe('Test Content')
        ->and($communication->status)->toBe(CommunicationStatus::Sent)
        ->and($communication->read_at)->toBeNull();
});

it('casts sent_at to datetime', function (): void {
    $communication = Communication::factory()->create(['sent_at' => now()]);

    expect($communication->sent_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts delivered_at to datetime', function (): void {
    $communication = Communication::factory()->create(['delivered_at' => now()]);

    expect($communication->delivered_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts read_at to datetime', function (): void {
    $communication = Communication::factory()->create(['read_at' => now()]);

    expect($communication->read_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $communication = Communication::factory()->create(['customer_id' => $customer->id]);

    expect($communication->customer)->toBeInstanceOf(Customer::class)
        ->and($communication->customer->id)->toBe($customer->id);
});
