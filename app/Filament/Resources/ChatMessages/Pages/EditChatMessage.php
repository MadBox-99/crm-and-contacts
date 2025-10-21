<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatMessages\Pages;

use App\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditChatMessage extends EditRecord
{
    protected static string $resource = ChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
