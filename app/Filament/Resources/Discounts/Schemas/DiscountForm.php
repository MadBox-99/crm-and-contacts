<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discounts\Schemas;

use App\Enums\DiscountType;
use App\Enums\DiscountValueType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->required()
                    ->default(DiscountType::Custom)
                    ->enum(DiscountType::class)
                    ->options(DiscountType::class),
                Select::make('value_type')
                    ->required()
                    ->default(DiscountValueType::Percentage)
                    ->enum(DiscountValueType::class)
                    ->options(DiscountValueType::class),
                TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_quantity')
                    ->numeric(),
                TextInput::make('min_value')
                    ->numeric(),
                DatePicker::make('valid_from'),
                DatePicker::make('valid_until'),
                Select::make('customer_id')
                    ->relationship('customer', 'name'),
                Select::make('product_id')
                    ->relationship('product', 'name'),
                Toggle::make('is_active')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
