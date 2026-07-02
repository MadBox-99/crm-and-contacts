<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyLevels\Pages;

use App\Filament\Resources\LoyaltyLevels\LoyaltyLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListLoyaltyLevels extends ListRecords
{
    protected static string $resource = LoyaltyLevelResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
