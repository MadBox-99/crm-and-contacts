<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

final class InvoiceService
{
    public static function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $lastInvoice = Invoice::query()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice
            ? ((int) mb_substr((string) $lastInvoice->invoice_number, -4)) + 1
            : 1;

        return 'INV-'.$year.'-'.mb_str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function createFromOrder(Order $order, int $dueDays = 30): Invoice
    {
        return DB::transaction(function () use ($order, $dueDays): Invoice {
            $order->load(['orderItems', 'customer']);

            $invoice = Invoice::query()->create([
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'invoice_number' => self::generateInvoiceNumber(),
                'issue_date' => now(),
                'due_date' => now()->addDays($dueDays),
                'status' => InvoiceStatus::Draft,
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'tax_amount' => $order->tax_amount,
                'total' => $order->total,
                'notes' => 'Generated from Order #'.$order->order_number,
            ]);

            foreach ($order->orderItems as $orderItem) {
                $invoice->invoiceItems()->create([
                    'product_id' => $orderItem->product_id,
                    'description' => $orderItem->description,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount_amount' => $orderItem->discount_amount,
                    'tax_rate' => $orderItem->tax_rate,
                    'total' => $orderItem->total,
                ]);
            }

            return $invoice->refresh();
        });
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['customer.addresses', 'invoiceItems', 'order']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ]);

        $filename = 'invoices/'.$invoice->invoice_number.'.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        $files = $invoice->files ?? [];
        $files[] = $filename;
        $invoice->update(['files' => $files]);

        return Storage::disk('local')->path($filename);
    }

    public function prepareNavXml(Invoice $invoice): string
    {
        $invoice->load(['customer', 'invoiceItems']);

        $customer = $invoice->customer;
        $billingAddress = $customer->addresses()->where('type', 'billing')->first();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><InvoiceData/>');
        $xml->addAttribute('xmlns', 'http://schemas.nav.gov.hu/OSA/3.0/data');

        $invoiceMain = $xml->addChild('invoiceMain');
        $invoiceEl = $invoiceMain->addChild('invoice');

        // Invoice head
        $head = $invoiceEl->addChild('invoiceHead');
        $head->addChild('invoiceNumber', $invoice->invoice_number);
        $head->addChild('invoiceIssueDate', $invoice->issue_date->format('Y-m-d'));
        $head->addChild('completionDate', $invoice->issue_date->format('Y-m-d'));
        $head->addChild('paymentDate', $invoice->due_date->format('Y-m-d'));

        // Supplier info
        $supplier = $head->addChild('supplierInfo');
        $supplier->addChild('supplierName', Config::get('app.name'));

        // Customer info
        $customerInfo = $head->addChild('customerInfo');
        $customerInfo->addChild('customerName', htmlspecialchars((string) $customer->name));

        if ($customer->tax_number) {
            $taxNumber = $customerInfo->addChild('customerTaxNumber');
            $taxNumber->addChild('taxpayerId', $customer->tax_number);
        }

        if ($customer->eu_tax_number) {
            $customerInfo->addChild('communityVatNumber', $customer->eu_tax_number);
        }

        if ($billingAddress) {
            $address = $customerInfo->addChild('customerAddress');
            $address->addChild('postalCode', $billingAddress->postal_code ?? '');
            $address->addChild('city', $billingAddress->city ?? '');
            $address->addChild('streetName', $billingAddress->street ?? '');
        }

        // Invoice lines
        $lines = $invoiceEl->addChild('invoiceLines');
        foreach ($invoice->invoiceItems as $index => $item) {
            $line = $lines->addChild('line');
            $line->addChild('lineNumber', (string) ($index + 1));
            $line->addChild('lineDescription', htmlspecialchars($item->description ?? ''));
            $line->addChild('quantity', number_format((float) $item->quantity, 2, '.', ''));
            $line->addChild('unitPrice', number_format((float) $item->unit_price, 2, '.', ''));
            $line->addChild('lineNetAmount', number_format((float) ($item->unit_price * $item->quantity - $item->discount_amount), 2, '.', ''));

            $vatRate = $line->addChild('lineVatRate');
            $vatRate->addChild('vatPercentage', number_format((float) $item->tax_rate, 2, '.', ''));

            $lineGross = ($item->unit_price * $item->quantity - $item->discount_amount) * (1 + (float) $item->tax_rate / 100);
            $line->addChild('lineGrossAmount', number_format($lineGross, 2, '.', ''));
        }

        // Invoice summary
        $summary = $invoiceEl->addChild('invoiceSummary');
        $summary->addChild('invoiceNetAmount', number_format((float) ($invoice->subtotal - $invoice->discount_amount), 2, '.', ''));
        $summary->addChild('invoiceVatAmount', number_format((float) $invoice->tax_amount, 2, '.', ''));
        $summary->addChild('invoiceGrossAmount', number_format((float) $invoice->total, 2, '.', ''));

        return (string) $xml->asXML();
    }
}
