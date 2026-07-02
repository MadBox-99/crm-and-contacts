<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class OrderService
{
    public static function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $lastOrder = Order::query()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastOrder
            ? ((int) mb_substr((string) $lastOrder->order_number, -4)) + 1
            : 1;

        return 'ORD-'.$year.'-'.mb_str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param  array<int, array{product_id?: int|null, description?: string|null, quantity: float, unit_price: float, discount_amount?: float, tax_rate?: float}>  $items
     * @param  array<string, mixed>  $attributes
     */
    public function createOrder(Customer $customer, array $items, array $attributes = []): Order
    {
        return DB::transaction(function () use ($customer, $items, $attributes): Order {
            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'order_number' => self::generateOrderNumber(),
                'order_date' => now(),
                'status' => OrderStatus::Pending,
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => 0,
                ...$attributes,
            ]);

            foreach ($items as $item) {
                $subtotal = ($item['unit_price'] * $item['quantity']);
                $discount = $item['discount_amount'] ?? 0;
                $taxRate = $item['tax_rate'] ?? 0;
                $afterDiscount = $subtotal - $discount;
                $total = $afterDiscount + ($afterDiscount * ($taxRate / 100));

                $order->orderItems()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $discount,
                    'tax_rate' => $taxRate,
                    'total' => $total,
                ]);
            }

            $this->recalculateTotals($order);

            return $order->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateOrder(Order $order, array $attributes): Order
    {
        return DB::transaction(function () use ($order, $attributes): Order {
            $order->update($attributes);

            return $order->refresh();
        });
    }

    public function transitionStatus(Order $order, OrderStatus $newStatus): Order
    {
        if (! $order->status->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                sprintf('Cannot transition order from %s to %s.', $order->status->value, $newStatus->value)
            );
        }

        $order->update(['status' => $newStatus]);

        return $order->refresh();
    }

    public function createFromQuote(Quote $quote): Order
    {
        throw_if($quote->status !== QuoteStatus::Accepted, InvalidArgumentException::class, 'Only accepted quotes can be converted to orders.');

        return DB::transaction(function () use ($quote): Order {
            $quote->load('items');

            $order = Order::query()->create([
                'customer_id' => $quote->customer_id,
                'quote_id' => $quote->id,
                'order_number' => self::generateOrderNumber(),
                'order_date' => now(),
                'status' => OrderStatus::Pending,
                'subtotal' => $quote->subtotal,
                'discount_amount' => $quote->discount_amount,
                'tax_amount' => $quote->tax_amount,
                'total' => $quote->total,
                'notes' => 'Generated from Quote #'.$quote->quote_number,
            ]);

            foreach ($quote->items as $quoteItem) {
                $order->orderItems()->create([
                    'product_id' => $quoteItem->product_id,
                    'description' => $quoteItem->description,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->unit_price,
                    'discount_amount' => $quoteItem->discount_amount,
                    'tax_rate' => $quoteItem->tax_rate,
                    'total' => $quoteItem->total,
                ]);
            }

            return $order->refresh();
        });
    }

    private function recalculateTotals(Order $order): void
    {
        $items = $order->orderItems;

        $subtotal = $items->sum(fn ($item): float => (float) $item->unit_price * (float) $item->quantity);
        $discountAmount = $items->sum(fn ($item): float => (float) $item->discount_amount);
        $taxAmount = $items->sum(function ($item): float {
            $afterDiscount = ((float) $item->unit_price * (float) $item->quantity) - (float) $item->discount_amount;

            return $afterDiscount * ((float) $item->tax_rate / 100);
        });

        $order->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $subtotal - $discountAmount + $taxAmount,
        ]);
    }
}
