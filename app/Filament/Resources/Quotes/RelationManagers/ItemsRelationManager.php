<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\RelationManagers;

use App\Models\QuoteItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateQuoteItemTotals($get, $set))
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live()
                    ->minValue(0.01),
                TextInput::make('unit_price')
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateQuoteItemTotals($get, $set))
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->live()
                    ->minValue(0),
                TextInput::make('discount_percent')
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateQuoteItemTotals($get, $set))
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->live()
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('discount_amount')
                    ->readOnly()
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->minValue(0),
                TextInput::make('tax_rate')
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateQuoteItemTotals($get, $set))
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->live()
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('total')
                    ->required()
                    ->live()
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('quantity')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('discount_percent')
                    ->suffix('%')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('discount_amount')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tax_rate')
                    ->suffix('%')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total')
                    ->money('USD'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public function calculateQuoteTotals(): void
    {
        $quote = $this->ownerRecord;

        $subtotal = $quote->items->sum('unit_price * quantity');
        $discountAmount = $quote->items->sum('discount_amount');
        $taxAmount = $quote->items->sum(fn (QuoteItem $item): int|float => ($item->unit_price * $item->quantity - $item->discount_amount) * ($item->tax_rate / 100));
        $total = $subtotal - $discountAmount + $taxAmount;

        $quote->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);
    }

    public function calculateQuoteItemTotals(Get $get, Set $set): void
    {
        $quantity = $get('quantity') ?? 0;
        $unitPrice = $get('unit_price') ?? 0;
        $discountPercent = $get('discount_percent') ?? 0;
        $taxRate = $get('tax_rate') ?? 0;

        $discountAmount = ($quantity * $unitPrice) * ($discountPercent / 100);
        $total = ($quantity * $unitPrice) - $discountAmount + (($quantity * $unitPrice - $discountAmount) * ($taxRate / 100));

        $set('discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }
}
