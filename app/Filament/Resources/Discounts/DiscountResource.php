<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discounts;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Discounts\Pages\CreateDiscount;
use App\Filament\Resources\Discounts\Pages\EditDiscount;
use App\Filament\Resources\Discounts\Pages\ListDiscounts;
use App\Filament\Resources\Discounts\Schemas\DiscountForm;
use App\Filament\Resources\Discounts\Tables\DiscountsTable;
use App\Models\Discount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

final class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Discounts;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Discount List');
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Discount');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('Discounts');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return DiscountForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return DiscountsTable::configure($table);
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
            'index' => ListDiscounts::route('/'),
            'create' => CreateDiscount::route('/create'),
            'edit' => EditDiscount::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
