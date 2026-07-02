<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Enums\NavigationGroup;
use App\Enums\Role;
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
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    #[Override]
    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole(Role::Admin) ?? false;
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    #[Override]
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

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
