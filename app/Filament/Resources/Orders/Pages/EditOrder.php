<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Carrier;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderService;
use App\Services\ShipmentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Override;

/** @property Order $record */
final class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('transition_status')
                ->label(__('Status'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->schema(fn (Order $record): array => [
                    Select::make('status')
                        ->label(__('New Status'))
                        ->options(
                            collect($record->status->allowedTransitions())
                                ->mapWithKeys(fn (OrderStatus $status): array => [$status->value => $status->getLabel()])
                                ->toArray()
                        )
                        ->required(),
                ])
                ->action(function (array $data, Order $record, OrderService $orderService): void {
                    $newStatus = OrderStatus::from($data['status']);
                    $orderService->transitionStatus($record, $newStatus);

                    Notification::make()
                        ->success()
                        ->title(__('Status Updated'))
                        ->body(__('Order status changed to :status.', ['status' => $newStatus->getLabel()]))
                        ->send();

                    $this->refreshFormData(['status']);
                })
                ->visible(fn (Order $record): bool => count($record->status->allowedTransitions()) > 0),

            Action::make('generate_invoice')
                ->label(__('Invoice'))
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('Invoice'))
                ->modalDescription(__('This will create a new invoice with all order items. Are you sure?'))
                ->modalSubmitActionLabel(__('Invoice'))
                ->action(function (Order $record, InvoiceService $invoiceService): void {
                    if ($record->invoices()->exists()) {
                        Notification::make()
                            ->warning()
                            ->title(__('Invoice Already Exists'))
                            ->body(__('An invoice has already been generated for this order.'))
                            ->send();

                        return;
                    }

                    $invoice = $invoiceService->createFromOrder($record);

                    Notification::make()
                        ->success()
                        ->title(__('Invoice Generated'))
                        ->body(__('Invoice :number created with :count items.', ['number' => $invoice->invoice_number, 'count' => $invoice->invoiceItems->count()]))
                        ->send();

                    $this->redirect(InvoiceResource::getUrl('edit', ['record' => $invoice]));
                })
                ->visible(fn (Order $record): bool => ! $record->invoices()->exists()),

            Action::make('generate_shipment')
                ->label(__('Shipments'))
                ->icon('heroicon-o-truck')
                ->color('info')
                ->schema([
                    Select::make('carrier')
                        ->label(__('Carrier'))
                        ->options(fn (): array => Carrier::query()
                            ->where('is_active', true)
                            ->pluck('name', 'name')
                            ->toArray())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, Order $record, ShipmentService $shipmentService): void {
                    $shipment = $shipmentService->createFromOrder($record, $data['carrier']);

                    Notification::make()
                        ->success()
                        ->title(__('Shipment Created'))
                        ->body(__('Shipment :number created with carrier :carrier.', ['number' => $shipment->shipment_number, 'carrier' => $shipment->carrier]))
                        ->send();

                    $this->redirect(ShipmentResource::getUrl('edit', ['record' => $shipment]));
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
