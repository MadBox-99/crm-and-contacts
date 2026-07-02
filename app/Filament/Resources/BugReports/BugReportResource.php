<?php

declare(strict_types=1);

namespace App\Filament\Resources\BugReports;

use App\Enums\NavigationGroup;
use App\Filament\Resources\BugReports\Pages\CreateBugReport;
use App\Filament\Resources\BugReports\Pages\EditBugReport;
use App\Filament\Resources\BugReports\Pages\ListBugReports;
use App\Filament\Resources\BugReports\Schemas\BugReportForm;
use App\Filament\Resources\BugReports\Tables\BugReportsTable;
use App\Models\BugReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBugAnt;

    protected static bool $shouldRegisterNavigation = false;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Bug Reports');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return BugReportForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return BugReportsTable::configure($table);
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
            'index' => ListBugReports::route('/'),
            'create' => CreateBugReport::route('/create'),
            'edit' => EditBugReport::route('/{record}/edit'),
        ];
    }
}
