<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Complaints\Pages\CreateComplaint;
use App\Filament\Resources\Complaints\Pages\EditComplaint;
use App\Filament\Resources\Complaints\Pages\ListComplaints;
use App\Filament\Resources\Complaints\Schemas\ComplaintForm;
use App\Filament\Resources\Complaints\Tables\ComplaintsTable;
use App\Models\Complaint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Complaints;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Complaints');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Complaint');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Complaints');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return ComplaintForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return ComplaintsTable::configure($table);
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
            'index' => ListComplaints::route('/'),
            'create' => CreateComplaint::route('/create'),
            'edit' => EditComplaint::route('/{record}/edit'),
        ];
    }
}
