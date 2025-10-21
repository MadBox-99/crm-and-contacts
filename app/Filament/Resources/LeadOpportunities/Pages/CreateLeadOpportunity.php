<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\Pages;

use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateLeadOpportunity extends CreateRecord
{
    protected static string $resource = LeadOpportunitiesResource::class;
}
