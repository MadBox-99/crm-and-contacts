<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\OpportunityStage;
use App\Models\Customer;
use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class OverviewStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected ?string $pollingInterval = '30s';

    #[Override]
    protected function getStats(): array
    {
        return [
            Stat::make(__('Customers'), (string) Customer::query()->count())
                ->icon('heroicon-o-users')
                ->color('danger'),
            Stat::make(__('Opportunities'), (string) Opportunity::query()->count())
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make(__('Open opportunities'), (string) Opportunity::query()->whereIn('stage', OpportunityStage::getActiveStages())->count())
                ->icon('heroicon-o-clock')
                ->color('info'),
            Stat::make(__('Closed opportunities'), (string) Opportunity::query()->whereIn('stage', OpportunityStage::getClosedStages())->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
