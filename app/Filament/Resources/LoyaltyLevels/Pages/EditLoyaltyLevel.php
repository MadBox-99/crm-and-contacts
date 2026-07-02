<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyLevels\Pages;

use App\Filament\Resources\LoyaltyLevels\LoyaltyLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditLoyaltyLevel extends EditRecord
{
    protected static string $resource = LoyaltyLevelResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
