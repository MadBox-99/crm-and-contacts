<?php

declare(strict_types=1);

use App\Enums\ChatMessageSenderType;
use App\Enums\ChatSessionStatus;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Support\Sleep;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->chatService = app(ChatService::class);
    $this->customer = Customer::factory()->create();
    $this->user = User::factory()->create();
});

test('can start a new chat session', function (): void {
    $session = $this->chatService->startSession($this->customer, $this->user);

    expect($session)
        ->toBeInstanceOf(ChatSession::class)
        ->customer_id->toBe($this->customer->id)
        ->user_id->toBe($this->user->id)
        ->status->toBe(ChatSessionStatus::Active)
        ->started_at->not->toBeNull();

    assertDatabaseHas('chat_sessions', [
        'id' => $session->id,
        'customer_id' => $this->customer->id,
        'user_id' => $this->user->id,
    ]);
});

test('can send a message in a session', function (): void {
    $session = $this->chatService->startSession($this->customer);

    $message = $this->chatService->sendMessage(
        $session,
        'Hello, I need help!',
        ChatMessageSenderType::Customer,
        $this->customer->id
    );

    expect($message)
        ->toBeInstanceOf(ChatMessage::class)
        ->message->toBe('Hello, I need help!')
        ->sender_type->toBe(ChatMessageSenderType::Customer)
        ->sender_id->toBe($this->customer->id)
        ->is_read->toBeFalse();

    assertDatabaseHas('chat_messages', [
        'id' => $message->id,
        'chat_session_id' => $session->id,
        'message' => 'Hello, I need help!',
    ]);
});

test('can mark a message as read', function (): void {
    $session = $this->chatService->startSession($this->customer);
    $message = $this->chatService->sendMessage(
        $session,
        'Test message',
        ChatMessageSenderType::Customer,
        $this->customer->id
    );

    expect($message->is_read)->toBeFalse();

    $this->chatService->markMessageAsRead($message);

    $message->refresh();

    expect($message->is_read)->toBeTrue()
        ->and($message->read_at)->not->toBeNull();
});

test('can mark all messages as read', function (): void {
    $session = $this->chatService->startSession($this->customer);

    $this->chatService->sendMessage($session, 'Message 1', ChatMessageSenderType::Customer, $this->customer->id);
    $this->chatService->sendMessage($session, 'Message 2', ChatMessageSenderType::Customer, $this->customer->id);
    $this->chatService->sendMessage($session, 'Message 3', ChatMessageSenderType::Customer, $this->customer->id);

    $this->chatService->markAllMessagesAsRead($session, ChatMessageSenderType::Customer);

    $unreadCount = ChatMessage::query()
        ->where('chat_session_id', $session->id)
        ->where('sender_type', ChatMessageSenderType::Customer)
        ->where('is_read', false)
        ->count();

    expect($unreadCount)->toBe(0);
});

test('can close a session', function (): void {
    $session = $this->chatService->startSession($this->customer);

    expect($session->status)->toBe(ChatSessionStatus::Active);

    $this->chatService->closeSession($session);

    $session->refresh();

    expect($session->status)->toBe(ChatSessionStatus::Closed)
        ->and($session->ended_at)->not->toBeNull();
});

test('can transfer session to another user', function (): void {
    $session = $this->chatService->startSession($this->customer, $this->user);
    $newUser = User::factory()->create();

    expect($session->user_id)->toBe($this->user->id);

    $this->chatService->transferSession($session, $newUser);

    $session->refresh();

    expect($session->user_id)->toBe($newUser->id);
});

test('can rate a session', function (): void {
    $session = $this->chatService->startSession($this->customer);

    expect($session->rating)->toBeNull();

    $this->chatService->rateSession($session, 5);

    $session->refresh();

    expect($session->rating)->toBe(5);
});

test('can get active sessions', function (): void {
    ChatSession::factory()->create(['status' => ChatSessionStatus::Active]);
    ChatSession::factory()->create(['status' => ChatSessionStatus::Active]);
    ChatSession::factory()->create(['status' => ChatSessionStatus::Closed]);

    $activeSessions = $this->chatService->getActiveSessions();

    expect($activeSessions)->toHaveCount(2);
});

test('can get unassigned sessions', function (): void {
    ChatSession::factory()->create(['user_id' => null, 'status' => ChatSessionStatus::Active]);
    ChatSession::factory()->create(['user_id' => null, 'status' => ChatSessionStatus::Active]);
    ChatSession::factory()->create(['user_id' => $this->user->id, 'status' => ChatSessionStatus::Active]);

    $unassignedSessions = $this->chatService->getUnassignedSessions();

    expect($unassignedSessions)->toHaveCount(2);
});

test('can assign session to user', function (): void {
    $session = ChatSession::factory()->create(['user_id' => null]);

    expect($session->user_id)->toBeNull();

    $this->chatService->assignSession($session, $this->user);

    $session->refresh();

    expect($session->user_id)->toBe($this->user->id);
});

test('sending message updates session last_message_at', function (): void {
    $session = $this->chatService->startSession($this->customer);

    $originalLastMessageAt = $session->last_message_at;

    Sleep::sleep(1);

    $this->chatService->sendMessage(
        $session,
        'Test message',
        ChatMessageSenderType::Customer,
        $this->customer->id
    );

    $session->refresh();

    expect($session->last_message_at)
        ->not->toBe($originalLastMessageAt)
        ->not->toBeNull();
});

test('can send reply to a message', function (): void {
    $session = $this->chatService->startSession($this->customer);

    $parentMessage = $this->chatService->sendMessage(
        $session,
        'Original question',
        ChatMessageSenderType::Customer,
        $this->customer->id
    );

    $replyMessage = $this->chatService->sendMessage(
        $session,
        'Reply to question',
        ChatMessageSenderType::User,
        $this->user->id,
        $parentMessage->id
    );

    expect($replyMessage->parent_message_id)->toBe($parentMessage->id);

    $parentMessage->refresh();

    expect($parentMessage->replies)->toHaveCount(1)
        ->and($parentMessage->replies->first()->id)->toBe($replyMessage->id);
});
