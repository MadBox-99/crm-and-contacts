<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\InvoiceStatus;
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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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

final class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Invoices');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label(__('Order'))
                    ->relationship('order', 'order_number')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->order_number, $record->customer?->name)),
                TextInput::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->required(),
                DatePicker::make('issue_date')
                    ->label(__('Issue Date'))
                    ->required(),
                DatePicker::make('due_date')
                    ->label(__('Due Date'))
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(InvoiceStatus::class)
                    ->default(InvoiceStatus::Draft)
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
                DateTimePicker::make('paid_at')
                    ->label(__('Paid At')),
                FileUpload::make('files')
                    ->label(__('Files'))
                    ->directory('invoices')
                    ->panelLayout('grid')
                    ->multiple()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                TextColumn::make('order.order_number')
                    ->label(__('Order'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label(__('Issue Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
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
                TextColumn::make('paid_at')
                    ->label(__('Paid At'))
                    ->dateTime()
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
