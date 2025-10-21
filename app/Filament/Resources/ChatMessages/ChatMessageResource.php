<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatMessages;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ChatMessages\Pages\CreateChatMessage;
use App\Filament\Resources\ChatMessages\Pages\EditChatMessage;
use App\Filament\Resources\ChatMessages\Pages\ListChatMessages;
use App\Filament\Resources\ChatMessages\Pages\ViewChatMessage;
use App\Filament\Resources\ChatMessages\Schemas\ChatMessageForm;
use App\Filament\Resources\ChatMessages\Schemas\ChatMessageInfolist;
use App\Filament\Resources\ChatMessages\Tables\ChatMessagesTable;
use App\Models\ChatMessage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class ChatMessageResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Support;

    public static function form(Schema $schema): Schema
    {
        return ChatMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChatMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatMessagesTable::configure($table);
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
            'index' => ListChatMessages::route('/'),
            'create' => CreateChatMessage::route('/create'),
            'view' => ViewChatMessage::route('/{record}'),
            'edit' => EditChatMessage::route('/{record}/edit'),
        ];
    }
}
