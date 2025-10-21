<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class ComplaintInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('order.id')
                    ->label('Order')
                    ->placeholder('-'),
                TextEntry::make('reported_by')
                    ->numeric(),
                TextEntry::make('assigned_to')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('severity'),
                TextEntry::make('status'),
                TextEntry::make('resolution')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('reported_at')
                    ->dateTime(),
                TextEntry::make('resolved_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
