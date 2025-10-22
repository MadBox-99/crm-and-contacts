<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Shipment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    protected static ?string $recordTitleAttribute = 'shipment_number';

    public function form(Schema $schema): Schema
    {
        return ShipmentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('shipment_number')
            ->columns([
                TextColumn::make('shipment_number')
                    ->label('Shipment #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->url(fn ($record) => ShipmentResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('carrier')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('tracking_number')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-truck')
                    ->mutateDataUsing(function (array $data, $livewire): array {
                        $data['customer_id'] = $livewire->ownerRecord->customer_id;
                        $data['order_id'] = $livewire->ownerRecord->id;
                        $data['shipment_number'] = Shipment::generateShipmentNumber();

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => ShipmentResource::getUrl('edit', ['record' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
