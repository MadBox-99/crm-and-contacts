<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateChatSession extends CreateRecord
{
    protected static string $resource = ChatSessionResource::class;
}
