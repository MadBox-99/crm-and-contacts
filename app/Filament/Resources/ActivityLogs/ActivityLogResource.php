<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActivityLogs;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

final class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $modelLabel = 'Activity';

    protected static ?string $pluralModelLabel = 'Activities';

    protected static ?int $navigationSort = 100;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isScopedToTenant = false;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Activity Log');
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }

    #[Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[Override]
    public static function canEdit($record): bool
    {
        return false;
    }

    #[Override]
    public static function canDelete($record): bool
    {
        return false;
    }
}
