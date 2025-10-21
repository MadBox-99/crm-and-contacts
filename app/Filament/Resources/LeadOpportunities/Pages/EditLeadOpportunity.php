<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\Pages;

use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditLeadOpportunity extends EditRecord
{
    protected static string $resource = LeadOpportunitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
