<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\SalesDashboardService;
use Filament\Widgets\ChartWidget;
use Override;

final class SalesTrendWidget extends ChartWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '30s';

    #[Override]
    public function getHeading(): ?string
    {
        return __('Monthly Sales Trend');
    }

    #[Override]
    protected function getData(): array
    {
        $service = resolve(SalesDashboardService::class);
        $trend = $service->getTrendData(12);

        $items = $trend['trend'] ?? [];

        return [
            'datasets' => [
                [
                    'label' => __('Orders Revenue'),
                    'data' => array_map(fn (array $item): float => $item['orders_revenue'], $items),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('Invoices Total'),
                    'data' => array_map(fn (array $item): float => $item['invoices_total'], $items),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_map(fn (array $item): string => $item['label'], $items),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
