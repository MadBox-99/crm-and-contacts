<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProposalOpportunities\Pages;

use App\Filament\Resources\ProposalOpportunities\ProposalOpportunitiesResource;
use Filament\Resources\Pages\ListRecords;

final class ManageProposalOpportunities extends ListRecords
{
    protected static string $resource = ProposalOpportunitiesResource::class;
}
