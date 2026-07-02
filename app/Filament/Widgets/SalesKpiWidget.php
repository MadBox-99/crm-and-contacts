<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\SalesDashboardService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class SalesKpiWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    #[Override]
    protected function getStats(): array
    {
        $service = resolve(SalesDashboardService::class);
        $kpis = $service->getKpis(1);

        return [
            Stat::make(__('Revenue'), number_format($kpis['monthly_revenue'], 0).' Ft')
                ->description(__('Last 1 month'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make(__('Active Quotes'), (string) $kpis['active_quotes'])
                ->description(__('Draft'))
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make(__('Conversion Rate'), $kpis['conversion_rate'].'%')
                ->description(__('Quotes').' → '.__('Orders'))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('warning'),
            Stat::make(__('Avg Deal Size'), number_format($kpis['avg_deal_size'], 0).' Ft')
                ->description(__('Orders'))
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
        ];
    }
}
