<?php

declare(strict_types=1);

namespace App\Filament\Resources\Interactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class InteractionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('type'),
                TextEntry::make('subject'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('interaction_date')
                    ->dateTime(),
                TextEntry::make('duration')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('next_action')
                    ->placeholder('-'),
                TextEntry::make('next_action_date')
                    ->date()
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
