<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListChatSessions extends ListRecords
{
    protected static string $resource = ChatSessionResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
