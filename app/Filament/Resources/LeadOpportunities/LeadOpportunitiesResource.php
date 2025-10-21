<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LeadOpportunities\Pages\CreateLeadOpportunity;
use App\Filament\Resources\LeadOpportunities\Pages\EditLeadOpportunity;
use App\Filament\Resources\LeadOpportunities\Pages\ListLeadOpportunities;
use App\Filament\Resources\LeadOpportunities\RelationManagers\QuotesRelationManager;
use App\Filament\Resources\LeadOpportunities\Schemas\LeadOpportunityForm;
use App\Filament\Resources\LeadOpportunities\Tables\LeadOpportunitiesTable;
use App\Models\Opportunity;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class LeadOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static ?string $navigationLabel = 'Leads';

    protected static ?string $modelLabel = 'Lead Opportunity';

    protected static ?string $pluralModelLabel = 'Lead Opportunities';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return LeadOpportunityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadOpportunitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            QuotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadOpportunities::route('/'),
            'create' => CreateLeadOpportunity::route('/create'),
            'edit' => EditLeadOpportunity::route('/{record}/edit'),
        ];
    }
}
