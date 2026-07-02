<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class GenerateInvoicePdfController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function __invoke(Invoice $invoice): BinaryFileResponse
    {
        $this->authorize('view', $invoice);

        $path = $this->invoiceService->generatePdf($invoice);

        return response()->download($path, $invoice->invoice_number.'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
