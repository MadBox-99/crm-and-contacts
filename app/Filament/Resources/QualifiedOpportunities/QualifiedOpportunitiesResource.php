<?php

declare(strict_types=1);

namespace App\Filament\Resources\QualifiedOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\QualifiedOpportunities\Pages\ManageQualifiedOpportunities;
use App\Filament\Resources\QualifiedOpportunities\Tables\QualifiedOpportunitiesTable;
use App\Models\Opportunity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class QualifiedOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Qualified Opportunity';

    protected static ?string $pluralModelLabel = 'Qualified Opportunities';

    protected static ?int $navigationSort = 11;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Qualified');
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return QualifiedOpportunitiesTable::configure($table);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ManageQualifiedOpportunities::route('/'),
        ];
    }
}
