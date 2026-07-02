<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Filament\Resources\Shipments\RelationManagers\TrackingEventsRelationManager;
use App\Filament\Resources\Shipments\Schemas\ShipmentForm;
use App\Filament\Resources\Shipments\Tables\ShipmentsTable;
use App\Models\Shipment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

final class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?int $navigationSort = 4;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Shipments');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Shipment');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Shipments');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return ShipmentForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return ShipmentsTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            TrackingEventsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListShipments::route('/'),
            'create' => CreateShipment::route('/create'),
            'edit' => EditShipment::route('/{record}/edit'),
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
