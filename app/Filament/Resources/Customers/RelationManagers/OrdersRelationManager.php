<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\OrderStatus;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

final class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Orders');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quote_id')
                    ->label(__('Quote'))
                    ->relationship('quote', 'quote_number')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->quote_number, $record->customer?->name)),
                TextInput::make('order_number')
                    ->label(__('Order Number'))
                    ->required(),
                DatePicker::make('order_date')
                    ->label(__('Order Date'))
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(OrderStatus::class)
                    ->default(OrderStatus::Pending)
                    ->required(),
                TextInput::make('subtotal')
                    ->label(__('Subtotal'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label(__('Discount Amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label(__('Total'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->columns([
                TextColumn::make('quote.quote_number')
                    ->label(__('Quote'))
                    ->searchable(),
                TextColumn::make('order_number')
                    ->label(__('Order Number'))
                    ->searchable(),
                TextColumn::make('order_date')
                    ->label(__('Order Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(__('Discount Amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
