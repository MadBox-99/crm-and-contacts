<?php

declare(strict_types=1);

use App\Enums\InteractionType;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $interaction = Interaction::factory()->create();

    expect($interaction)->toBeInstanceOf(Interaction::class)
        ->and($interaction->subject)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $user = User::factory()->create();

    $interaction = Interaction::factory()->create([
        'customer_id' => $customer->id,
        'user_id' => $user->id,
        'type' => InteractionType::Meeting,
        'subject' => 'Test Meeting',
        'description' => 'Test Description',
        'interaction_date' => now(),
        'duration' => 60,
        'next_action' => 'Follow up',
        'next_action_date' => now()->addDays(7),
    ]);

    expect($interaction->customer_id)->toBe($customer->id)
        ->and($interaction->user_id)->toBe($user->id)
        ->and($interaction->type)->toBe(InteractionType::Meeting)
        ->and($interaction->subject)->toBe('Test Meeting')
        ->and($interaction->description)->toBe('Test Description')
        ->and($interaction->duration)->toBe(60)
        ->and($interaction->next_action)->toBe('Follow up');
});

it('casts interaction_date to datetime', function (): void {
    $interaction = Interaction::factory()->create(['interaction_date' => now()]);

    expect($interaction->interaction_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts next_action_date to date', function (): void {
    $interaction = Interaction::factory()->create(['next_action_date' => '2025-02-15']);

    expect($interaction->next_action_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts duration to integer', function (): void {
    $interaction = Interaction::factory()->create(['duration' => 60]);

    expect($interaction->duration)->toBe(60)
        ->and($interaction->duration)->toBeInt();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $interaction = Interaction::factory()->create(['customer_id' => $customer->id]);

    expect($interaction->customer)->toBeInstanceOf(Customer::class)
        ->and($interaction->customer->id)->toBe($customer->id);
});

it('has user relationship', function (): void {
    $user = User::factory()->create();
    $interaction = Interaction::factory()->create(['user_id' => $user->id]);

    expect($interaction->user)->toBeInstanceOf(User::class)
        ->and($interaction->user->id)->toBe($user->id);
});
