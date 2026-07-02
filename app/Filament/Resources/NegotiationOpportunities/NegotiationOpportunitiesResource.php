<?php

declare(strict_types=1);

namespace App\Filament\Resources\NegotiationOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\NegotiationOpportunities\Pages\ManageNegotiationOpportunities;
use App\Filament\Resources\NegotiationOpportunities\Tables\NegotiationOpportunitiesTable;
use App\Models\Opportunity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class NegotiationOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Negotiation Opportunity';

    protected static ?string $pluralModelLabel = 'Negotiation Opportunities';

    protected static ?int $navigationSort = 13;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Negotiations');
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return NegotiationOpportunitiesTable::configure($table);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ManageNegotiationOpportunities::route('/'),
        ];
    }
}
