<?php

declare(strict_types=1);

use App\Enums\ChatMessageSenderType;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $chatMessage = ChatMessage::factory()->create();

    expect($chatMessage)->toBeInstanceOf(ChatMessage::class)
        ->and($chatMessage->message)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $chatSession = ChatSession::factory()->create();
    $user = User::factory()->create();

    $chatMessage = ChatMessage::factory()->create([
        'chat_session_id' => $chatSession->id,
        'sender_type' => ChatMessageSenderType::User,
        'sender_id' => $user->id,
        'message' => 'Hello, how can I help you?',
    ]);

    expect($chatMessage->chat_session_id)->toBe($chatSession->id)
        ->and($chatMessage->sender_type)->toBe(ChatMessageSenderType::User)
        ->and($chatMessage->sender_id)->toBe($user->id)
        ->and($chatMessage->message)->toBe('Hello, how can I help you?');
});

it('has chatSession relationship', function (): void {
    $chatSession = ChatSession::factory()->create();
    $chatMessage = ChatMessage::factory()->create(['chat_session_id' => $chatSession->id]);

    expect($chatMessage->chatSession)->toBeInstanceOf(ChatSession::class)
        ->and($chatMessage->chatSession->id)->toBe($chatSession->id);
});

it('has polymorphic sender relationship with User', function (): void {
    $user = User::factory()->create();
    $chatMessage = ChatMessage::factory()->create([
        'sender_type' => 'user',
        'sender_id' => $user->id,
    ]);

    expect($chatMessage->sender)->toBeInstanceOf(User::class)
        ->and($chatMessage->sender->id)->toBe($user->id);
});

it('has polymorphic sender relationship with Customer', function (): void {
    $customer = Customer::factory()->create();
    $chatMessage = ChatMessage::factory()->create([
        'sender_type' => 'customer',
        'sender_id' => $customer->id,
    ]);

    expect($chatMessage->sender)->toBeInstanceOf(Customer::class)
        ->and($chatMessage->sender->id)->toBe($customer->id);
});
