<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ChatMessageSenderType;
use App\Enums\ChatSessionStatus;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ChatService
{
    public function startSession(Customer $customer, ?User $assignedUser = null): ChatSession
    {
        return DB::transaction(fn (): ChatSession => ChatSession::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $assignedUser?->id,
            'started_at' => now(),
            'status' => ChatSessionStatus::Active,
            'priority' => 'normal',
            'unread_count' => 0,
        ]));
    }

    public function sendMessage(ChatSession $session, string $message, ChatMessageSenderType $senderType, int $senderId, ?int $parentMessageId = null): ChatMessage
    {
        return DB::transaction(
            function () use ($session, $message, $senderType, $senderId, $parentMessageId): ChatMessage {
                $chatMessage = ChatMessage::query()->create([
                    'chat_session_id' => $session->id,
                    'parent_message_id' => $parentMessageId,
                    'sender_type' => $senderType,
                    'sender_id' => $senderId,
                    'message' => $message,
                    'is_read' => false,
                ]);

                // Update session last_message_at and unread_count
                $session->update([
                    'last_message_at' => now(),
                    'unread_count' => $senderType === ChatMessageSenderType::Customer
                        ? $session->unread_count + 1
                        : $session->unread_count,
                ]);

                // Broadcast event
                broadcast(new MessageSent($chatMessage))->toOthers();

                return $chatMessage;
            });
    }

    public function markMessageAsRead(ChatMessage $message): void
    {
        if (! $message->is_read) {
            DB::transaction(function () use ($message): void {
                $message->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

                // Decrement unread count
                $session = $message->chatSession;
                if ($session->unread_count > 0) {
                    $session->decrement('unread_count');
                }
            });
        }
    }

    public function markAllMessagesAsRead(ChatSession $session, ChatMessageSenderType $senderType): void
    {
        DB::transaction(function () use ($session, $senderType): void {
            $session->messages()
                ->where('sender_type', $senderType)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            $session->update(['unread_count' => 0]);
        });
    }

    public function closeSession(ChatSession $session): void
    {
        $session->update([
            'status' => ChatSessionStatus::Closed,
            'ended_at' => now(),
        ]);
    }

    public function transferSession(ChatSession $session, User $newUser): void
    {
        $session->update([
            'user_id' => $newUser->id,
            'status' => ChatSessionStatus::Transferred,
        ]);
    }

    public function rateSession(ChatSession $session, int $rating): void
    {
        throw_if($rating < 1 || $rating > 5, InvalidArgumentException::class, 'Rating must be between 1 and 5');

        $session->update(['rating' => $rating]);
    }

    public function getActiveSessions(): Collection
    {
        return ChatSession::with(['customer', 'user', 'messages'])
            ->where('status', ChatSessionStatus::Active)
            ->latest('last_message_at')
            ->get();
    }

    public function getUnassignedSessions(): Collection
    {
        return ChatSession::with(['customer', 'messages'])
            ->where('status', ChatSessionStatus::Active)
            ->whereNull('user_id')->oldest()
            ->get();
    }

    public function assignSession(ChatSession $session, User $user): void
    {
        $session->update(['user_id' => $user->id]);
    }
}
