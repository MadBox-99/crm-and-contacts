<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings;

use App\Enums\NavigationGroup;
use App\Filament\Resources\FormCrmSettings\Pages\CreateFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\EditFormCrmSetting;
use App\Filament\Resources\FormCrmSettings\Pages\ListFormCrmSettings;
use App\Filament\Resources\FormCrmSettings\Schemas\FormCrmSettingForm;
use App\Filament\Resources\FormCrmSettings\Tables\FormCrmSettingsTable;
use App\Models\FormCrmSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class FormCrmSettingResource extends Resource
{
    protected static ?string $model = FormCrmSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Form CRM Settings');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Form CRM Setting');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Form CRM Settings');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return FormCrmSettingForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return FormCrmSettingsTable::configure($table);
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
            'index' => ListFormCrmSettings::route('/'),
            'create' => CreateFormCrmSetting::route('/create'),
            'edit' => EditFormCrmSetting::route('/{record}/edit'),
        ];
    }
}
