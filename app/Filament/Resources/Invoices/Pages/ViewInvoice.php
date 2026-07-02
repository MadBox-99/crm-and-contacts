<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @property Invoice $record */
final class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (Invoice $record, InvoiceService $invoiceService): BinaryFileResponse {
                    $path = $invoiceService->generatePdf($record);

                    return response()->download($path, $record->invoice_number.'.pdf');
                }),
            EditAction::make(),
        ];
    }
}
