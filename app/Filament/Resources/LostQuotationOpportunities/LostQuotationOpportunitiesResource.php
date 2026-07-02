<?php

declare(strict_types=1);

namespace App\Filament\Resources\LostQuotationOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LostQuotationOpportunities\Pages\ManageLostQuotationOpportunities;
use App\Filament\Resources\LostQuotationOpportunities\Tables\LostQuotationOpportunitiesTable;
use App\Models\Opportunity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class LostQuotationOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedXCircle;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Lost Quotation Opportunity';

    protected static ?string $pluralModelLabel = 'Lost Quotation Opportunities';

    protected static ?int $navigationSort = 15;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Lost Quotation');
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return LostQuotationOpportunitiesTable::configure($table);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ManageLostQuotationOpportunities::route('/'),
        ];
    }
}
