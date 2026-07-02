<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\SalesDashboardService;
use Filament\Widgets\ChartWidget;
use Override;

final class CampaignRoiWidget extends ChartWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '30s';

    #[Override]
    public function getHeading(): string
    {
        return __('Campaign ROI');
    }

    #[Override]
    protected function getData(): array
    {
        $service = resolve(SalesDashboardService::class);
        $campaigns = $service->getCampaignRoiData();

        return [
            'datasets' => [
                [
                    'label' => __('Budget'),
                    'data' => array_map(fn (array $c): float => $c['budget'], $campaigns),
                    'backgroundColor' => 'rgba(156, 163, 175, 0.7)',
                    'borderRadius' => 4,
                ],
                [
                    'label' => __('Revenue'),
                    'data' => array_map(fn (array $c): float => $c['revenue'], $campaigns),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_map(fn (array $c): string => $c['name'], $campaigns),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    #[Override]
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
