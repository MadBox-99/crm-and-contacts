<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyLevels;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LoyaltyLevels\Pages\CreateLoyaltyLevel;
use App\Filament\Resources\LoyaltyLevels\Pages\EditLoyaltyLevel;
use App\Filament\Resources\LoyaltyLevels\Pages\ListLoyaltyLevels;
use App\Filament\Resources\LoyaltyLevels\Schemas\LoyaltyLevelForm;
use App\Filament\Resources\LoyaltyLevels\Tables\LoyaltyLevelsTable;
use App\Models\LoyaltyLevel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class LoyaltyLevelResource extends Resource
{
    protected static ?string $model = LoyaltyLevel::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Loyalty;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Loyalty Levels');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Loyalty Level');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Loyalty Levels');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return LoyaltyLevelForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return LoyaltyLevelsTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyLevels::route('/'),
            'create' => CreateLoyaltyLevel::route('/create'),
            'edit' => EditLoyaltyLevel::route('/{record}/edit'),
        ];
    }
}
