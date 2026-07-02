<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\QuoteStatus;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quote;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class DocumentChainService
{
    public function __construct(
        private OrderService $orderService,
        private InvoiceService $invoiceService,
        private ShipmentService $shipmentService,
    ) {}

    public function createOrderFromQuote(Quote $quote): Order
    {
        throw_if($quote->status !== QuoteStatus::Accepted, InvalidArgumentException::class, 'Only accepted quotes can be converted to orders.');

        return $this->orderService->createFromQuote($quote);
    }

    public function createInvoiceFromOrder(Order $order, int $dueDays = 30): Invoice
    {
        return $this->invoiceService->createFromOrder($order, $dueDays);
    }

    /**
     * @param  array<string, mixed>|null  $shippingAddress
     */
    public function createShipmentFromOrder(Order $order, string $carrier, ?array $shippingAddress = null): Shipment
    {
        return $this->shipmentService->createFromOrder($order, $carrier, $shippingAddress);
    }

    /**
     * @param  array<string, mixed>|null  $shippingAddress
     * @return array{order: Order, invoice: Invoice, shipment: Shipment}
     */
    public function processFullChain(Quote $quote, string $carrier, int $dueDays = 30, ?array $shippingAddress = null): array
    {
        return DB::transaction(function () use ($quote, $carrier, $dueDays, $shippingAddress): array {
            $order = $this->createOrderFromQuote($quote);
            $invoice = $this->createInvoiceFromOrder($order, $dueDays);
            $shipment = $this->createShipmentFromOrder($order, $carrier, $shippingAddress);

            return [
                'order' => $order,
                'invoice' => $invoice,
                'shipment' => $shipment,
            ];
        });
    }
}
