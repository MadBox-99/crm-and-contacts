<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Override;

final class FormSubmissionTrendWidget extends ChartWidget
{
    protected static ?int $sort = 8;

    protected ?string $pollingInterval = '60s';

    #[Override]
    public function getHeading(): string
    {
        return __('Form submissions (30 days)');
    }

    #[Override]
    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return ['datasets' => [], 'labels' => []];
        }

        $trend = resolve(FormSubmissionMetricsService::class)->dailyTrend($tenant->id);

        return [
            'datasets' => [
                [
                    'label' => __('Submissions'),
                    'data' => $trend['values'],
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
