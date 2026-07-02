<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use Filament\Widgets\Widget;
use Override;

final class QuickActionsWidget extends Widget
{
    protected static ?int $sort = -1;

    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function getViewData(): array
    {
        return [
            'newOpportunityUrl' => LeadOpportunitiesResource::getUrl('create'),
            'newCustomerUrl' => CustomerResource::getUrl('create'),
            'opportunitiesUrl' => LeadOpportunitiesResource::getUrl('index'),
            'customersUrl' => CustomerResource::getUrl('index'),
        ];
    }
}
