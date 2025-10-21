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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Support;

    public static function form(Schema $schema): Schema
    {
        return ComplaintForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintsTable::configure($table);
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
            'index' => ListComplaints::route('/'),
            'create' => CreateComplaint::route('/create'),
            'edit' => EditComplaint::route('/{record}/edit'),
        ];
    }
}
