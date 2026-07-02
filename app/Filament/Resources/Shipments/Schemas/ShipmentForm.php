<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas;

use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Shipment Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('customer_id')
                                    ->label(__('Customer'))
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('order_id')
                                    ->label(__('Order'))
                                    ->relationship('order', 'order_number')
                                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->order_number, $record->customer?->name))
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('external_customer_id')
                                    ->label(__('External Customer ID')),

                                TextInput::make('external_order_id')
                                    ->label(__('External Order ID')),

                                TextInput::make('shipment_number')
                                    ->label(__('Shipment number'))
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('carrier')
                                    ->label(__('Carrier'))
                                    ->options(fn (): array => Carrier::query()
                                        ->where('is_active', true)
                                        ->pluck('name', 'name')
                                        ->toArray())
                                    ->searchable(),

                                TextInput::make('tracking_number')
                                    ->label(__('Tracking'))
                                    ->scopedUnique(ignoreRecord: true)
                                    ->maxLength(255),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(ShipmentStatus::class)
                                    ->default(ShipmentStatus::Pending->value)
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('Shipping Address'))
                    ->schema([
                        KeyValue::make('shipping_address')
                            ->label('')
                            ->keyLabel(__('Field'))
                            ->valueLabel(__('Value'))
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('Dates & Timeline'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('shipped_at')
                                    ->label(__('Shipped at')),

                                DateTimePicker::make('estimated_delivery_at')
                                    ->label(__('Estimated Delivery')),

                                DateTimePicker::make('delivered_at')
                                    ->label(__('Delivered at')),
                            ]),
                    ])
                    ->collapsible(),

                Section::make(__('Additional Information'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),

                        KeyValue::make('documents')
                            ->label(__('Documents & Attachments'))
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
