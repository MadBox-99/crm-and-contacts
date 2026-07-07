<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListFormCrmSettings extends ListRecords
{
    protected static string $resource = FormCrmSettingResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
