<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuotationSendedOpportunities\Pages;

use App\Filament\Resources\QuotationSendedOpportunities\QuotationSendedOpportunitiesResource;
use Filament\Resources\Pages\ListRecords;

final class ManageQuotationSendedOpportunities extends ListRecords
{
    protected static string $resource = QuotationSendedOpportunitiesResource::class;
}
