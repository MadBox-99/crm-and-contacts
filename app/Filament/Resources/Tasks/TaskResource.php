<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tasks;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Models\Task;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 4;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Tasks');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Task');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Tasks');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
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
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
