<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\RelationManagers;

use App\Enums\OrderStatus;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

final class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // This is read-only, orders are managed separately
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('Order Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order_date')
                    ->label(__('Order Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('discount_amount')
                    ->label(__('Discount Amount'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->multiple(),
            ])
            ->headerActions([
                // No create/edit actions - orders are created separately
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
