<?php

declare(strict_types=1);

use App\Enums\ChatSessionStatus;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $chatSession = ChatSession::factory()->create();

    expect($chatSession)->toBeInstanceOf(ChatSession::class);
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $user = User::factory()->create();

    $chatSession = ChatSession::factory()->create([
        'customer_id' => $customer->id,
        'user_id' => $user->id,
        'started_at' => now(),
        'ended_at' => null,
        'status' => ChatSessionStatus::Active,
    ]);

    expect($chatSession->customer_id)->toBe($customer->id)
        ->and($chatSession->user_id)->toBe($user->id)
        ->and($chatSession->status)->toBe(ChatSessionStatus::Active)
        ->and($chatSession->ended_at)->toBeNull();
});

it('casts started_at to datetime', function (): void {
    $chatSession = ChatSession::factory()->create(['started_at' => now()]);

    expect($chatSession->started_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts ended_at to datetime', function (): void {
    $chatSession = ChatSession::factory()->create(['ended_at' => now()]);

    expect($chatSession->ended_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $chatSession = ChatSession::factory()->create(['customer_id' => $customer->id]);

    expect($chatSession->customer)->toBeInstanceOf(Customer::class)
        ->and($chatSession->customer->id)->toBe($customer->id);
});

it('has user relationship', function (): void {
    $user = User::factory()->create();
    $chatSession = ChatSession::factory()->create(['user_id' => $user->id]);

    expect($chatSession->user)->toBeInstanceOf(User::class)
        ->and($chatSession->user->id)->toBe($user->id);
});

it('has messages relationship', function (): void {
    $chatSession = ChatSession::factory()->create();
    $message = ChatMessage::factory()->create(['chat_session_id' => $chatSession->id]);

    expect($chatSession->messages)->toHaveCount(1)
        ->and($chatSession->messages->first()->id)->toBe($message->id);
});
