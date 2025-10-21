<?php

declare(strict_types=1);

namespace App\Filament\Resources\Communications\Pages;

use App\Filament\Resources\Communications\CommunicationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCommunication extends CreateRecord
{
    protected static string $resource = CommunicationResource::class;
}
