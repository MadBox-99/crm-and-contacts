<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatMessages\Pages;

use App\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateChatMessage extends CreateRecord
{
    protected static string $resource = ChatMessageResource::class;
}
