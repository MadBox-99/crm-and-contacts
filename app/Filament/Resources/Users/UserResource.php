<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\RelationManagers\BugReportsRelationManager;
use App\Filament\Resources\Users\RelationManagers\ChatSessionsRelationManager;
use App\Filament\Resources\Users\RelationManagers\InteractionsRelationManager;
use App\Filament\Resources\Users\RelationManagers\PermissionsRelationManager;
use App\Filament\Resources\Users\RelationManagers\RolesRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
            PermissionsRelationManager::class,
            InteractionsRelationManager::class,
            BugReportsRelationManager::class,
            ChatSessionsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
