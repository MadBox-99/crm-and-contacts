<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

/** @property Order $ownerRecord */
final class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    #[Override]
    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('Order Items');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('Product'))
                    ->live()
                    ->relationship('product', 'name'),
                RichEditor::make('description')
                    ->label(__('Description')),
                TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->live()
                    ->required()
                    ->integer()
                    ->numeric()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        if ($get('product_id') && $get('unit_price') < 0 && $get('unit_price')) {
                            $set('unit_price', $this->ownerRecord->customer->getPriceForProduct($get('product_id')));

                        }

                        if ($state && $get('unit_price')) {
                            $set('total', $state * $get('unit_price'));
                        }
                    })
                    ->default(1),
                TextInput::make('unit_price')
                    ->label(__('Unit price'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label(__('Discount amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_rate')
                    ->label(__('Tax rate'))
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
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(__('Unit price'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(__('Discount amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_rate')
                    ->label(__('Tax rate'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('New Item')),
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
