<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class ChatMessageList extends Component
{
    public ChatSession $session;

    public function mount(ChatSession $session): void
    {
        $this->session = $session;
        $this->markMessagesAsRead();
    }

    #[On('message-sent')]
    public function refreshMessages(): void
    {
        $this->session->refresh();
        $this->session->load(['messages.sender']);
        $this->markMessagesAsRead();

        $this->dispatch('messages-refreshed');
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:chat.session.{session.id},.message.sent')]
    public function handleNewMessage(): void
    {
        $this->refreshMessages();
    }

    public function markMessagesAsRead(): void
    {
        $chatService = app(ChatService::class);

        $unreadMessages = $this->session->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->get();

        foreach ($unreadMessages as $message) {
            $chatService->markMessageAsRead($message);
        }
    }

    public function render(): View
    {
        $messages = $this->session->messages()
            ->with('sender')
            ->oldest()
            ->get();

        return view('livewire.chat.chat-message-list', [
            'messages' => $messages,
        ]);
    }
}
