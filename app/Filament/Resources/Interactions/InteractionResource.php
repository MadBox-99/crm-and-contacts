<?php

declare(strict_types=1);

namespace App\Filament\Resources\Interactions;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Interactions\Pages\CreateInteraction;
use App\Filament\Resources\Interactions\Pages\EditInteraction;
use App\Filament\Resources\Interactions\Pages\ListInteractions;
use App\Filament\Resources\Interactions\Schemas\InteractionForm;
use App\Filament\Resources\Interactions\Tables\InteractionsTable;
use App\Models\Interaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Marketing;

    public static function form(Schema $schema): Schema
    {
        return InteractionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InteractionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractions::route('/'),
            'create' => CreateInteraction::route('/create'),
            'edit' => EditInteraction::route('/{record}/edit'),
        ];
    }
}
