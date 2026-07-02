<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoyaltyPoints;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LoyaltyPoints\Pages\ListLoyaltyPoints;
use App\Filament\Resources\LoyaltyPoints\Tables\LoyaltyPointsTable;
use App\Models\LoyaltyPoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

final class LoyaltyPointResource extends Resource
{
    protected static ?string $model = LoyaltyPoint::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Loyalty;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 2;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Loyalty Points');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Loyalty Point');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Loyalty Points');
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return LoyaltyPointsTable::configure($table);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyPoints::route('/'),
        ];
    }
}
