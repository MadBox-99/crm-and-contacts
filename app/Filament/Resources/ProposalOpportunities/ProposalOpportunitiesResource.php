<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProposalOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ProposalOpportunities\Pages\ManageProposalOpportunities;
use App\Filament\Resources\ProposalOpportunities\Tables\ProposalOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

final class ProposalOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Proposals';

    protected static ?string $modelLabel = 'Proposal Opportunity';

    protected static ?string $pluralModelLabel = 'Proposal Opportunities';

    protected static ?int $navigationSort = 12;

    public static function table(Table $table): Table
    {
        return ProposalOpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProposalOpportunities::route('/'),
        ];
    }
}
