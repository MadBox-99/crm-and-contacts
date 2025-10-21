<?php

declare(strict_types=1);

namespace App\Filament\Resources\NegotiationOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\NegotiationOpportunities\Pages\ManageNegotiationOpportunities;
use App\Filament\Resources\NegotiationOpportunities\Tables\NegotiationOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

final class NegotiationOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Negotiations';

    protected static ?string $modelLabel = 'Negotiation Opportunity';

    protected static ?string $pluralModelLabel = 'Negotiation Opportunities';

    protected static ?int $navigationSort = 13;

    public static function table(Table $table): Table
    {
        return NegotiationOpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNegotiationOpportunities::route('/'),
        ];
    }
}
