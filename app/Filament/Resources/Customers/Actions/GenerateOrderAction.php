<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Actions;

use App\Enums\OrderStatus;
use App\Enums\QuoteStatus;
use App\Models\Order;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class GenerateOrderAction
{
    public static function make(): Action
    {
        return Action::make('generate_order')
            ->label('Generate Order')
            ->icon('heroicon-o-shopping-cart')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Generate Order from Quote')
            ->modalDescription('This will create a new order based on this quote data.')
            ->modalSubmitActionLabel('Generate Order')
            ->action(function (Quote $record): void {

                // Generate unique order number
                $lastOrder = Order::query()
                    ->whereYear('created_at', now()->year)
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastOrder ? ((int) mb_substr((string) $lastOrder->order_number, -4)) + 1 : 1;
                $orderNumber = 'ORD-'.now()->year.'-'.mb_str_pad(
                    (string) $nextNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                // Create order from quote data
                $order = Order::query()->create([
                    'customer_id' => $record->customer_id,
                    'quote_id' => $record->id,
                    'order_number' => $orderNumber,
                    'order_date' => now(),
                    'status' => OrderStatus::Pending,
                    'subtotal' => $record->subtotal,
                    'discount_amount' => $record->discount_amount,
                    'tax_amount' => $record->tax_amount,
                    'total' => $record->total,
                    'notes' => 'Generated from Quote #'.$record->quote_number.($record->notes ? '

'.$record->notes : ''),
                ]);

                // Copy quote items to order items
                foreach ($record->items as $quoteItem) {
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

                $itemCount = $record->items->count();

                Notification::make()
                    ->success()
                    ->title('Order Generated Successfully')
                    ->body(sprintf('Order #%s has been created with %s ', $order->order_number, $itemCount).str('item')->plural($itemCount).' and a total value of {$orderNumber} HUF.')
                    ->send();
            })
            ->visible(fn (Quote $record): bool => $record->status === QuoteStatus::Accepted && ! $record->orders()->exists());
    }
}
