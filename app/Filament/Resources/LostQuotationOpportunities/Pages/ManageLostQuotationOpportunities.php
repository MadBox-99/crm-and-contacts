<?php

declare(strict_types=1);

namespace App\Filament\Resources\LostQuotationOpportunities\Pages;

use App\Filament\Resources\LostQuotationOpportunities\LostQuotationOpportunitiesResource;
use Filament\Resources\Pages\ListRecords;

final class ManageLostQuotationOpportunities extends ListRecords
{
    protected static string $resource = LostQuotationOpportunitiesResource::class;
}
