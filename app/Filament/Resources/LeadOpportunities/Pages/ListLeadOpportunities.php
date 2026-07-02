<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\Pages;

use App\Filament\Imports\OpportunityImporter;
use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListLeadOpportunities extends ListRecords
{
    protected static string $resource = LeadOpportunitiesResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(OpportunityImporter::class),
        ];
    }
}
