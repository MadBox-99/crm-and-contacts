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
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class LeadOpportunitiesResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static ?int $navigationSort = 2;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Leads / Opportunities');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Lead');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Leads');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return LeadOpportunityForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return LeadOpportunitiesTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            QuotesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLeadOpportunities::route('/'),
            'create' => CreateLeadOpportunity::route('/create'),
            'edit' => EditLeadOpportunity::route('/{record}/edit'),
        ];
    }
}
