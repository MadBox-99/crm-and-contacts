<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Pages;

use App\Filament\Resources\FormCrmSettings\FormCrmSettingResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateFormCrmSetting extends CreateRecord
{
    protected static string $resource = FormCrmSettingResource::class;
}
