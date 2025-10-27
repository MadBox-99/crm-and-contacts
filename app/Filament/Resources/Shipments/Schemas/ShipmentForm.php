<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\Schemas;

use App\Enums\ShipmentStatus;
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
                Section::make('Shipment Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('order_id')
                                    ->relationship('order', 'order_number')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('external_customer_id')
                                    ->label('External Customer ID')
                                    ->helperText('ID from external warehouse system'),

                                TextInput::make('external_order_id')
                                    ->label('External Order ID')
                                    ->helperText('ID from external warehouse system'),

                                TextInput::make('shipment_number')
                                    ->label('Shipment Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Auto-generated'),

                                TextInput::make('carrier')
                                    ->label('Carrier')
                                    ->placeholder('GLS, DPD, FoxPost...')
                                    ->maxLength(255),

                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Select::make('status')
                                    ->options(ShipmentStatus::class)
                                    ->default(ShipmentStatus::Pending->value)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Shipping Address')
                    ->schema([
                        KeyValue::make('shipping_address')
                            ->label('')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Dates & Timeline')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('shipped_at')
                                    ->label('Shipped At'),

                                DateTimePicker::make('estimated_delivery_at')
                                    ->label('Estimated Delivery'),

                                DateTimePicker::make('delivered_at')
                                    ->label('Delivered At'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        KeyValue::make('documents')
                            ->label('Documents & Attachments')
                            ->keyLabel('Type')
                            ->valueLabel('URL')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
