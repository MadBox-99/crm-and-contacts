<?php

declare(strict_types=1);

namespace App\Filament\Resources\LostQuotationOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LostQuotationOpportunities\Pages\ManageLostQuotationOpportunities;
use App\Filament\Resources\LostQuotationOpportunities\Tables\LostQuotationOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

final class LostQuotationOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Lost Quotation';

    protected static ?string $modelLabel = 'Lost Quotation Opportunity';

    protected static ?string $pluralModelLabel = 'Lost Quotation Opportunities';

    protected static ?int $navigationSort = 15;

    public static function table(Table $table): Table
    {
        return LostQuotationOpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLostQuotationOpportunities::route('/'),
        ];
    }
}
