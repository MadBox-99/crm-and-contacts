<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class FormSubmissionStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 7;

    protected ?string $pollingInterval = '60s';

    #[Override]
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return [];
        }

        $stats = resolve(FormSubmissionMetricsService::class)->stats($tenant->id);

        return [
            Stat::make(__('Submissions today'), (string) $stats['today'])
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info'),
            Stat::make(__('Submissions this week'), (string) $stats['week'])
                ->icon('heroicon-o-calendar-days')
                ->color('gray'),
            Stat::make(__('Lead conversion'), $stats['conversion_rate'].'%')
                ->description($stats['converted'].' / '.$stats['total'])
                ->icon('heroicon-o-user-plus')
                ->color('success'),
        ];
    }
}
