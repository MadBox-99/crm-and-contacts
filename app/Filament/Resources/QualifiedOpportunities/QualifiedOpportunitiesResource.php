<?php

declare(strict_types=1);

namespace App\Filament\Resources\QualifiedOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\QualifiedOpportunities\Pages\ManageQualifiedOpportunities;
use App\Filament\Resources\QualifiedOpportunities\Tables\QualifiedOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

final class QualifiedOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Qualified';

    protected static ?string $modelLabel = 'Qualified Opportunity';

    protected static ?string $pluralModelLabel = 'Qualified Opportunities';

    protected static ?int $navigationSort = 11;

    public static function table(Table $table): Table
    {
        return QualifiedOpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQualifiedOpportunities::route('/'),
        ];
    }
}
