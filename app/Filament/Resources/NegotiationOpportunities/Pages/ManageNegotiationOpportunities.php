<?php

declare(strict_types=1);

namespace App\Filament\Resources\NegotiationOpportunities\Pages;

use App\Filament\Resources\NegotiationOpportunities\NegotiationOpportunitiesResource;
use Filament\Resources\Pages\ListRecords;

final class ManageNegotiationOpportunities extends ListRecords
{
    protected static string $resource = NegotiationOpportunitiesResource::class;
}
