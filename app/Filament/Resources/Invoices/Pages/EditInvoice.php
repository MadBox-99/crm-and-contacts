<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Override;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @property Invoice $record */
final class EditInvoice extends EditRecord
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
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
