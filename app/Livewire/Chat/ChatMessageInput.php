<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Enums\ChatMessageSenderType;
use App\Events\UserTyping;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Services\ChatService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ChatMessageInput extends Component
{
    public ChatSession $session;

    public Customer $customer;

    #[Validate('required|string|max:1000')]
    public string $message = '';

    public bool $isTyping = false;

    public function mount(ChatSession $session, Customer $customer): void
    {
        $this->session = $session;
        $this->customer = $customer;
    }

    public function updatedMessage(): void
    {
        if (! $this->isTyping && $this->message !== '') {
            $this->isTyping = true;
            broadcast(new UserTyping(
                sessionId: $this->session->id,
                userName: $this->customer->name,
                isTyping: true
            ))->toOthers();

            $this->dispatch('typing-started');
        }

        if ($this->isTyping && $this->message === '') {
            $this->stopTyping();
        }
    }

    public function sendMessage(): void
    {
        $this->validate();

        if (in_array(mb_trim($this->message), ['', '0'], true)) {
            return;
        }

        $chatService = app(ChatService::class);

        $chatService->sendMessage(
            session: $this->session,
            message: mb_trim($this->message),
            senderType: ChatMessageSenderType::Customer,
            senderId: $this->customer->id
        );

        $this->stopTyping();
        $this->message = '';

        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
    }

    public function stopTyping(): void
    {
        if ($this->isTyping) {
            $this->isTyping = false;
            broadcast(new UserTyping(
                sessionId: $this->session->id,
                userName: $this->customer->name,
                isTyping: false
            ))->toOthers();

            $this->dispatch('typing-stopped');
        }
    }

    public function render(): View
    {
        return view('livewire.chat.chat-message-input');
    }
}
