<?php

declare(strict_types=1);

namespace App\Filament\Resources\Communications;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Communications\Pages\CreateCommunication;
use App\Filament\Resources\Communications\Pages\EditCommunication;
use App\Filament\Resources\Communications\Pages\ListCommunications;
use App\Filament\Resources\Communications\Pages\ViewCommunication;
use App\Filament\Resources\Communications\Schemas\CommunicationForm;
use App\Filament\Resources\Communications\Schemas\CommunicationInfolist;
use App\Filament\Resources\Communications\Tables\CommunicationsTable;
use App\Models\Communication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class CommunicationResource extends Resource
{
    protected static ?string $model = Communication::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    public static function form(Schema $schema): Schema
    {
        return CommunicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunicationsTable::configure($table);
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
            'index' => ListCommunications::route('/'),
            'create' => CreateCommunication::route('/create'),
            'view' => ViewCommunication::route('/{record}'),
            'edit' => EditCommunication::route('/{record}/edit'),
        ];
    }
}
