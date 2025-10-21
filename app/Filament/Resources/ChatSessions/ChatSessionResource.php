<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessions;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ChatSessions\Pages\CreateChatSession;
use App\Filament\Resources\ChatSessions\Pages\EditChatSession;
use App\Filament\Resources\ChatSessions\Pages\ListChatSessions;
use App\Filament\Resources\ChatSessions\Pages\ViewChatSession;
use App\Filament\Resources\ChatSessions\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\ChatSessions\Schemas\ChatSessionForm;
use App\Filament\Resources\ChatSessions\Tables\ChatSessionsTable;
use App\Models\ChatSession;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class ChatSessionResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Support;

    protected static ?string $navigationLabel = 'Chat Sessions';

    protected static ?string $modelLabel = 'Chat Session';

    protected static ?string $pluralModelLabel = 'Chat Sessions';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ChatSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatSessions::route('/'),
            'create' => CreateChatSession::route('/create'),
            'view' => ViewChatSession::route('/{record}'),
            'edit' => EditChatSession::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::query()
            ->where('status', 'active')
            ->whereNull('user_id')
            ->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        $count = self::getModel()::query()
            ->where('status', 'active')
            ->whereNull('user_id')
            ->count();

        return $count > 0 ? 'warning' : 'gray';
    }
}
