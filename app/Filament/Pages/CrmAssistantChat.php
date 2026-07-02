<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Ai\Agents\CrmAssistant;
use App\Enums\NavigationGroup;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Override;
use Throwable;
use UnitEnum;

final class CrmAssistantChat extends Page
{
    #[Validate('required|string|min:1|max:2000')]
    public string $message = '';

    /** @var list<array{role: string, content: string}> */
    public array $chatHistory = [];

    public ?string $conversationId = null;

    public bool $isLoading = false;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected string $view = 'filament.pages.crm-assistant-chat';

    protected static ?int $navigationSort = 100;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('AI Assistant');
    }

    #[Override]
    public function getTitle(): string
    {
        return __('CRM AI Assistant');
    }

    /**
     * @return Collection<int, object>
     */
    public function getConversationsProperty(): Collection
    {
        return DB::table('agent_conversations')
            ->where('user_id', Auth::id())
            ->latest('updated_at')
            ->limit(50)
            ->get(['id', 'title', 'created_at', 'updated_at']);
    }

    public function loadConversation(string $id): void
    {
        $this->conversationId = $id;

        $messages = DB::table('agent_conversation_messages')
            ->where('conversation_id', $id)->oldest()
            ->get(['role', 'content']);

        $this->chatHistory = $messages
            ->filter(fn ($msg): bool => in_array($msg->role, ['user', 'assistant'], true))
            ->map(fn ($msg): array => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->values()
            ->all();
    }

    public function newConversation(): void
    {
        $this->chatHistory = [];
        $this->conversationId = null;
        $this->message = '';
    }

    public function deleteConversation(string $id): void
    {
        DB::table('agent_conversation_messages')
            ->where('conversation_id', $id)
            ->delete();

        DB::table('agent_conversations')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        if ($this->conversationId === $id) {
            $this->newConversation();
        }

        Notification::make()
            ->title(__('Conversation deleted'))
            ->success()
            ->send();
    }

    public function sendMessage(): void
    {
        $this->validate();

        $userMessage = mb_trim($this->message);
        $this->message = '';

        $this->chatHistory[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        $this->isLoading = true;

        try {
            $agent = new CrmAssistant();
            $user = Auth::user();

            if ($this->conversationId !== null) {
                $response = $agent
                    ->continue($this->conversationId, as: $user)
                    ->prompt($userMessage);
            } else {
                $response = $agent
                    ->forUser($user)
                    ->prompt($userMessage);
            }

            $this->conversationId = $response->conversationId;

            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => (string) $response,
            ];
        } catch (Throwable $throwable) {
            $this->chatHistory[] = [
                'role' => 'assistant',
                'content' => __('Sorry, an error occurred. Please try again.'),
            ];

            Notification::make()
                ->title(__('AI Error'))
                ->body($throwable->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }
}
