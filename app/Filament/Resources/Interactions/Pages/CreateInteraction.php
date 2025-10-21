<?php

declare(strict_types=1);

namespace App\Filament\Resources\Interactions\Pages;

use App\Filament\Resources\Interactions\InteractionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateInteraction extends CreateRecord
{
    protected static string $resource = InteractionResource::class;
}
