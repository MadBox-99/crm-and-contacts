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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Support;

    public static function form(Schema $schema): Schema
    {
        return BugReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BugReportsTable::configure($table);
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
            'index' => ListBugReports::route('/'),
            'create' => CreateBugReport::route('/create'),
            'edit' => EditBugReport::route('/{record}/edit'),
        ];
    }
}
