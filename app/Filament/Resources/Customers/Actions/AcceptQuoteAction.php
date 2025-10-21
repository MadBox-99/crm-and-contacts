<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Actions;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class AcceptQuoteAction
{
    public static function make(): Action
    {
        return Action::make('accept_quote')
            ->label('Accept Quote')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->modalHeading('Accept Quote')
            ->action(function (Quote $record): void {
                $record->update([
                    'status' => QuoteStatus::Accepted,
                ]);
                Notification::make()
                    ->success()
                    ->title('Quote Accepted')
                    ->body(sprintf('Quote #%s has been accepted.', $record->quote_number))
                    ->send();
            })
            ->visible(fn (Quote $record): bool => in_array($record->status, [QuoteStatus::Draft, QuoteStatus::Sent]));
    }
}
