<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SalesIntegrationInterface;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SalesIntegrationService implements SalesIntegrationInterface
{
    /** @return array{success: bool, reference_id: string|null, message: string} */
    public function pushOrderToAccounting(Order $order): array
    {
        $order->load(['customer', 'orderItems']);

        $payload = [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer?->name,
            'customer_tax_number' => $order->customer?->tax_number,
            'order_date' => $order->order_date?->format('Y-m-d'),
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'tax_amount' => (float) $order->tax_amount,
            'total' => (float) $order->total,
            'items' => $order->orderItems->map(fn ($item): array => [
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'tax_rate' => (float) $item->tax_rate,
                'total' => (float) $item->total,
            ])->all(),
        ];

        return $this->sendToExternalSystem(
            config('services.accounting.url', ''),
            $payload,
            'pushOrderToAccounting',
            $order->order_number,
        );
    }

    /** @return array{success: bool, reference_id: string|null, message: string} */
    public function pushInvoiceToFinance(Invoice $invoice): array
    {
        $invoice->load(['customer', 'invoiceItems', 'order']);

        $payload = [
            'invoice_number' => $invoice->invoice_number,
            'order_number' => $invoice->order?->order_number,
            'customer_name' => $invoice->customer?->name,
            'customer_tax_number' => $invoice->customer?->tax_number,
            'issue_date' => $invoice->issue_date?->format('Y-m-d'),
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'subtotal' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'total' => (float) $invoice->total,
            'items' => $invoice->invoiceItems->map(fn ($item): array => [
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'tax_rate' => (float) $item->tax_rate,
                'total' => (float) $item->total,
            ])->all(),
        ];

        return $this->sendToExternalSystem(
            config('services.finance.url', ''),
            $payload,
            'pushInvoiceToFinance',
            $invoice->invoice_number,
        );
    }

    /** @return array{available: bool, quantity: int, warehouse: string|null} */
    public function checkInventory(Product $product, int $quantity = 1): array
    {
        $url = config('services.inventory.url', '');

        if (! $url) {
            Log::info('SalesIntegration: Inventory check skipped (no URL configured)', [
                'product_id' => $product->id,
            ]);

            return [
                'available' => true,
                'quantity' => $quantity,
                'warehouse' => null,
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withToken(config('services.inventory.token', ''))
                ->get($url.'/products/'.$product->id.'/stock', [
                    'quantity' => $quantity,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'available' => $data['available'] ?? false,
                    'quantity' => $data['quantity'] ?? 0,
                    'warehouse' => $data['warehouse'] ?? null,
                ];
            }

            return ['available' => false, 'quantity' => 0, 'warehouse' => null];
        } catch (Throwable $throwable) {
            Log::error('SalesIntegration: Inventory check failed', [
                'product_id' => $product->id,
                'error' => $throwable->getMessage(),
            ]);

            return ['available' => false, 'quantity' => 0, 'warehouse' => null];
        }
    }

    /** @return array{success: bool, reservation_id: string|null, message: string} */
    public function reserveStock(Order $order, array $items): array
    {
        $url = config('services.inventory.url', '');

        if (! $url) {
            Log::info('SalesIntegration: Stock reservation skipped (no URL configured)', [
                'order_id' => $order->id,
            ]);

            return [
                'success' => true,
                'reservation_id' => null,
                'message' => 'Reservation skipped (no inventory system configured)',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withToken(config('services.inventory.token', ''))
                ->post($url.'/reservations', [
                    'order_number' => $order->order_number,
                    'items' => $items,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'reservation_id' => $data['reservation_id'] ?? null,
                    'message' => 'Stock reserved successfully',
                ];
            }

            return [
                'success' => false,
                'reservation_id' => null,
                'message' => 'Reservation failed: '.$response->body(),
            ];
        } catch (Throwable $throwable) {
            Log::error('SalesIntegration: Stock reservation failed', [
                'order_id' => $order->id,
                'error' => $throwable->getMessage(),
            ]);

            return [
                'success' => false,
                'reservation_id' => null,
                'message' => $throwable->getMessage(),
            ];
        }
    }

    /**
     * Send data to an external system.
     *
     * @return array{success: bool, reference_id: string|null, message: string}
     */
    private function sendToExternalSystem(string $url, array $payload, string $operation, string $reference): array
    {
        if ($url === '' || $url === '0') {
            Log::info(sprintf('SalesIntegration: %s skipped (no URL configured)', $operation), [
                'reference' => $reference,
            ]);

            return [
                'success' => true,
                'reference_id' => null,
                'message' => $operation.' skipped (no external system configured)',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withToken(config('services.accounting.token', ''))
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info(sprintf('SalesIntegration: %s succeeded', $operation), [
                    'reference' => $reference,
                    'external_id' => $data['reference_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'reference_id' => $data['reference_id'] ?? null,
                    'message' => 'Successfully pushed to external system',
                ];
            }

            Log::warning(sprintf('SalesIntegration: %s failed', $operation), [
                'reference' => $reference,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'reference_id' => null,
                'message' => 'External system returned '.$response->status(),
            ];
        } catch (Throwable $throwable) {
            Log::error(sprintf('SalesIntegration: %s exception', $operation), [
                'reference' => $reference,
                'error' => $throwable->getMessage(),
            ]);

            return [
                'success' => false,
                'reference_id' => null,
                'message' => $throwable->getMessage(),
            ];
        }
    }
}
