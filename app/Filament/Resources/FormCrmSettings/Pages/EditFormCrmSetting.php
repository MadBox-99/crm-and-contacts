<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditFormCrmSetting extends EditRecord
{
    protected static string $resource = FormCrmSettingResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
