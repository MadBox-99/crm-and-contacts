<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discounts\Schemas;

use App\Models\Discount;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class DiscountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('type'),
                TextEntry::make('value_type'),
                TextEntry::make('value')
                    ->numeric(),
                TextEntry::make('min_quantity')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('min_value')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('valid_from')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('valid_until')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('customer.name')
                    ->label('Customer')
                    ->placeholder('-'),
                TextEntry::make('product.name')
                    ->label('Product')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Discount $record): bool => $record->trashed()),
            ]);
    }
}
