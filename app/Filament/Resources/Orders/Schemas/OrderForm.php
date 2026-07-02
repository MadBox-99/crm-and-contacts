<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

final class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('quote_id')
                    ->label(__('Quote'))
                    ->relationship('quote', 'quote_number')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->quote_number, $record->customer?->name))
                    ->searchable()
                    ->preload(),
                TextInput::make('order_number')
                    ->label(__('Order number'))
                    ->scopedUnique(ignoreRecord: true)
                    ->required(),
                DatePicker::make('order_date')
                    ->label(__('Order Date'))
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->required()
                    ->default(OrderStatus::Pending->value)
                    ->options(OrderStatus::class),
                TextInput::make('subtotal')
                    ->label(__('Subtotal'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                TextInput::make('discount_amount')
                    ->label(__('Discount amount'))
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                TextInput::make('tax_amount')
                    ->label(__('Tax amount'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                TextInput::make('total')
                    ->label(__('Total'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->readOnly(),
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull(),
            ]);
    }

    private static function recalculate(Get $get, Set $set): void
    {
        $subtotal = (float) ($get('subtotal') ?? 0);
        $discount = (float) ($get('discount_amount') ?? 0);
        $tax = (float) ($get('tax_amount') ?? 0);

        $set('total', number_format($subtotal - $discount + $tax, 2, '.', ''));
    }
}
