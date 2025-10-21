<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Models\ChatSession;
use App\Models\Customer;
use App\Services\ChatService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class ChatWidget extends Component
{
    public ?ChatSession $session = null;

    public ?Customer $customer = null;

    public bool $isOpen = false;

    public bool $isMinimized = false;

    public function mount(?int $customerId = null): void
    {
        if ($customerId !== null && $customerId !== 0) {
            $this->customer = Customer::query()->find($customerId);

            // Check for existing active session
            $this->session = ChatSession::query()
                ->where('customer_id', $customerId)
                ->whereIn('status', ['active', 'transferred'])
                ->latest()
                ->first();
        }
    }

    public function startChat(): void
    {
        if (! $this->customer instanceof Customer) {
            $this->dispatch('error', message: 'Customer not found');

            return;
        }

        $chatService = app(ChatService::class);
        $this->session = $chatService->startSession($this->customer);

        $this->isOpen = true;
        $this->dispatch('chat-started', sessionId: $this->session->id);
    }

    public function toggleChat(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen && ! $this->session instanceof ChatSession) {
            $this->startChat();
        }
    }

    public function minimizeChat(): void
    {
        $this->isMinimized = ! $this->isMinimized;
    }

    public function closeChat(): void
    {
        if ($this->session instanceof ChatSession) {
            $chatService = app(ChatService::class);
            $chatService->closeSession($this->session);
        }

        $this->isOpen = false;
        $this->session = null;
        $this->dispatch('chat-closed');
    }

    #[On('message-sent')]
    public function refreshSession(): void
    {
        if ($this->session instanceof ChatSession) {
            $this->session->refresh();
        }
    }

    public function render(): View
    {
        return view('livewire.chat.chat-widget');
    }
}
