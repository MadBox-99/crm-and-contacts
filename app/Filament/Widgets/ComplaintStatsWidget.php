<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\SalesDashboardService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class ComplaintStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected ?string $pollingInterval = '30s';

    #[Override]
    protected function getStats(): array
    {
        $service = resolve(SalesDashboardService::class);
        $stats = $service->getComplaintStats();

        return [
            Stat::make(__('Total'), (string) ($stats['total'] ?? 0))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('gray'),
            Stat::make(__('Overdue SLA'), (string) ($stats['overdue_count'] ?? 0))
                ->icon('heroicon-o-clock')
                ->color('danger'),
            Stat::make(__('Avg Resolution Time'), ($stats['avg_resolution_hours'] ?? 0).' '.__('hours_short'))
                ->icon('heroicon-o-check-circle')
                ->color('info'),
        ];
    }
}
