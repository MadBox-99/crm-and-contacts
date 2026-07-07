<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Services\FormSubmissionMetricsService;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Override;

final class FormSubmissionsByFormWidget extends ChartWidget
{
    protected static ?int $sort = 9;

    protected ?string $pollingInterval = '60s';

    #[Override]
    public function getHeading(): string
    {
        return __('Submissions by form');
    }

    #[Override]
    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant instanceof Team) {
            return ['datasets' => [], 'labels' => []];
        }

        $byForm = resolve(FormSubmissionMetricsService::class)->byForm($tenant->id);

        return [
            'datasets' => [
                [
                    'data' => $byForm['values'],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $byForm['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
