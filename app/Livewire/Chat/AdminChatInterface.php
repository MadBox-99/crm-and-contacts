<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Enums\ChatMessageSenderType;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class AdminChatInterface extends Component
{
    public ChatSession $session;

    #[Validate('required|string|max:1000')]
    public string $message = '';

    public bool $isTyping = false;

    public function mount(ChatSession $session): void
    {
        $this->session = $session;
    }

    public function sendMessage(): void
    {
        $this->validate();

        if (in_array(mb_trim($this->message), ['', '0'], true)) {
            return;
        }

        $chatService = app(ChatService::class);

        $chatService->sendMessage(
            $this->session,
            $this->message,
            ChatMessageSenderType::User,
            Auth::id()
        );

        $this->message = '';
        $this->isTyping = false;

        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:chat.session.{session.id},.message.sent')]
    public function handleNewMessage(): void
    {
        $this->refreshMessages();
    }

    public function refreshMessages(): void
    {
        $this->session->refresh();
        $this->session->load(['customer', 'user', 'messages.sender']);
        $this->dispatch('scroll-to-bottom');
    }

    public function markAllAsRead(): void
    {
        $chatService = app(ChatService::class);
        $chatService->markAllMessagesAsRead($this->session, ChatMessageSenderType::Customer);

        $this->session->refresh();
        $this->session->load(['messages.sender']);
    }

    public function updatedMessage(): void
    {
        $this->isTyping = $this->message !== '' && $this->message !== '0';
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.admin-chat-interface');
    }
}
