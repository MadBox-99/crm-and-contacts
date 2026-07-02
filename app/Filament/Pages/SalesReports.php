<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Filament\Widgets\CampaignRoiWidget;
use App\Filament\Widgets\ComplaintChartWidget;
use App\Filament\Widgets\ComplaintStatsWidget;
use App\Filament\Widgets\PipelineChartWidget;
use App\Filament\Widgets\SalesKpiWidget;
use App\Filament\Widgets\SalesTrendWidget;
use App\Filament\Widgets\TopCustomersWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;
use UnitEnum;

final class SalesReports extends Page
{
    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Reports;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales-reports';

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Sales Reports');
    }

    #[Override]
    public function getTitle(): string
    {
        return __('Sales Reports');
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            SalesKpiWidget::class,
        ];
    }

    #[Override]
    protected function getFooterWidgets(): array
    {
        return [
            PipelineChartWidget::class,
            SalesTrendWidget::class,
            CampaignRoiWidget::class,
            ComplaintStatsWidget::class,
            ComplaintChartWidget::class,
            TopCustomersWidget::class,
        ];
    }
}
