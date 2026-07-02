<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Filament\Resources\Campaigns\Pages\ViewCampaign;
use App\Filament\Resources\Campaigns\RelationManagers\ResponsesRelationManager;
use App\Filament\Resources\Campaigns\RelationManagers\TargetAudienceRelationManager;
use App\Filament\Resources\Campaigns\Schemas\CampaignForm;
use App\Filament\Resources\Campaigns\Schemas\CampaignInfolist;
use App\Filament\Resources\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

final class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Campaigns;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Campaign List');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Campaign');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Campaigns');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return CampaignForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return CampaignInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            TargetAudienceRelationManager::class,
            ResponsesRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
