<?php

declare(strict_types=1);

namespace App\Filament\Resources\QualifiedOpportunities\Pages;

use App\Filament\Resources\QualifiedOpportunities\QualifiedOpportunitiesResource;
use Filament\Resources\Pages\ListRecords;

final class ManageQualifiedOpportunities extends ListRecords
{
    protected static string $resource = QualifiedOpportunitiesResource::class;
}
