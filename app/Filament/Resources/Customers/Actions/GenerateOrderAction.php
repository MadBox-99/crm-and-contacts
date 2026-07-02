<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Actions;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use App\Services\DocumentChainService;
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
                $documentChainService = resolve(DocumentChainService::class);
                $order = $documentChainService->createOrderFromQuote($record);

                $itemCount = $order->orderItems()->count();

                Notification::make()
                    ->success()
                    ->title('Order Generated Successfully')
                    ->body(sprintf(
                        'Order #%s has been created with %d %s and a total value of %s HUF.',
                        $order->order_number,
                        $itemCount,
                        str('item')->plural($itemCount),
                        number_format((float) $order->total, 0, ',', ' '),
                    ))
                    ->send();
            })
            ->visible(fn (Quote $record): bool => $record->status === QuoteStatus::Accepted && ! $record->orders()->exists());
    }
}
