<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;
}
