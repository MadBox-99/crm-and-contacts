<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuotationSendedOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\QuotationSendedOpportunities\Pages\ManageQuotationSendedOpportunities;
use App\Filament\Resources\QuotationSendedOpportunities\Tables\QuotationSendedOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

final class QuotationSendedOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Quotation Sended';

    protected static ?string $modelLabel = 'Quotation Sended Opportunity';

    protected static ?string $pluralModelLabel = 'Quotation Sended Opportunities';

    protected static ?int $navigationSort = 14;

    public static function table(Table $table): Table
    {
        return QuotationSendedOpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuotationSendedOpportunities::route('/'),
        ];
    }
}
