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
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    protected static ?string $recordTitleAttribute = 'shipment_number';

    #[Override]
    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('Shipments');
    }

    #[Override]
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
                    ->label(__('Shipment #'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->url(fn (Model $record): ?string => $this->getShipmentEditUrl($record)),

                TextColumn::make('carrier')
                    ->label(__('Carrier'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('tracking_number')
                    ->label(__('Tracking number'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('shipped_at')
                    ->label(__('Shipped'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('delivered_at')
                    ->label(__('Delivered'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('New Shipment'))
                    ->icon('heroicon-o-truck')
                    ->mutateDataUsing(function (array $data, $livewire): array {
                        $data['customer_id'] = $livewire->ownerRecord->customer_id;
                        $data['order_id'] = $livewire->ownerRecord->id;
                        $data['team_id'] = Filament::getTenant()?->getKey();
                        $data['shipment_number'] = Shipment::generateShipmentNumber();

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Model $record): ?string => $this->getShipmentEditUrl($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private function getShipmentEditUrl(Model $record): ?string
    {
        $tenant = Filament::getTenant() ?? $this->ownerRecord->team;

        if (! $tenant) {
            return null;
        }

        return ShipmentResource::getUrl('edit', ['record' => $record], tenant: $tenant);
    }
}
