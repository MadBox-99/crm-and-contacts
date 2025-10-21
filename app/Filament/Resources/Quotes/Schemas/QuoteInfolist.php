<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Quote;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class QuoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('opportunity.title')
                    ->label('Opportunity')
                    ->placeholder('-'),
                TextEntry::make('quote_number'),
                TextEntry::make('issue_date')
                    ->date(),
                TextEntry::make('valid_until')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Quote $record): bool => $record->trashed()),
            ]);
    }
}
