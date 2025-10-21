<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Invoice;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/** @property Order $record  */
final class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_invoice')
                ->label('Generate Invoice')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Invoice from Order')
                ->modalDescription('This will create a new invoice based on this order data. Are you sure you want to continue?')
                ->modalSubmitActionLabel('Generate Invoice')
                ->action(function (Order $record): void {
                    // Check if an invoice already exists for this order
                    if ($record->invoices()->exists()) {
                        Notification::make()
                            ->warning()
                            ->title('Invoice Already Exists')
                            ->body('An invoice has already been generated for this order.')
                            ->send();

                        return;
                    }

                    // Generate unique invoice number
                    $lastInvoice = Invoice::query()
                        ->whereYear('created_at', now()->year)
                        ->orderBy('id', 'desc')
                        ->first();

                    $nextNumber = $lastInvoice ? ((int) mb_substr((string) $lastInvoice->invoice_number, -4)) + 1 : 1;
                    $invoiceNumber = 'INV-'.now()->year.'-'.mb_str_pad(
                        (string) $nextNumber,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                    // Create invoice from order data
                    $invoice = Invoice::query()->create([
                        'customer_id' => $record->customer_id,
                        'order_id' => $record->id,
                        'invoice_number' => $invoiceNumber,
                        'issue_date' => now(),
                        'due_date' => now()->addDays(30),
                        'status' => InvoiceStatus::Draft,
                        'subtotal' => $record->subtotal,
                        'discount_amount' => $record->discount_amount,
                        'tax_amount' => $record->tax_amount,
                        'total' => $record->total,
                        'notes' => $record->notes ? "Generated from Order #{$record->order_number}\n\n{$record->notes}" : 'Generated from Order #'.$record->order_number,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Invoice Generated Successfully')
                        ->body(sprintf('Invoice #%s has been created. Click here to view it.', $invoice->invoice_number))
                        ->send();

                    // Redirect to the invoice
                    $this->redirect(route('filament.admin.resources.invoices.edit', ['record' => $invoice->id]));
                })
                ->visible(fn (Order $record): bool => ! $record->invoices()->exists()),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
