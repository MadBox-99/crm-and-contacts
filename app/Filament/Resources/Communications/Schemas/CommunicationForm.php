<?php

declare(strict_types=1);

namespace App\Filament\Resources\Communications\Schemas;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class CommunicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name'),
                Select::make('channel')
                    ->options(CommunicationChannel::class)
                    ->default('email')
                    ->required(),
                Select::make('direction')
                    ->options(CommunicationDirection::class)
                    ->default('outbound')
                    ->required(),
                TextInput::make('subject'),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(CommunicationStatus::class)
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('read_at'),
            ]);
    }
}
