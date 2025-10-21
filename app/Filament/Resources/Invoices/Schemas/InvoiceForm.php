<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('order_id')
                    ->relationship('order', 'order_number'),
                TextInput::make('invoice_number')
                    ->unique(ignoreRecord: true)
                    ->required(),
                DatePicker::make('issue_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->enum(InvoiceStatus::class)
                    ->options(InvoiceStatus::class)
                    ->default(InvoiceStatus::Draft),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
                FileUpload::make('files')
                    ->directory('invoices')
                    ->panelLayout('grid')
                    ->multiple()
                    ->columnSpanFull(),
            ]);
    }
}
