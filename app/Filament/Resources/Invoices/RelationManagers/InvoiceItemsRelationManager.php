<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

final class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceItems';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('Product'))
                    ->relationship('product', 'name'),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->live()
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        if ($state && $get('unit_price')) {
                            $subtotal = (float) $state * (float) $get('unit_price');
                            $afterDiscount = $subtotal - (float) ($get('discount_amount') ?? 0);
                            $set('total', round($afterDiscount + ($afterDiscount * (float) ($get('tax_rate') ?? 0) / 100), 2));
                        }
                    })
                    ->default(1),
                TextInput::make('unit_price')
                    ->label(__('Unit Price'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label(__('Discount Amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_rate')
                    ->label(__('Tax Rate'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label(__('Total'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(__('Unit Price'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(__('Discount Amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_rate')
                    ->label(__('Tax Rate'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Item');
    }
}
