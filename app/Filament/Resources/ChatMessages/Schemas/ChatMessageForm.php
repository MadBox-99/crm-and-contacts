<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ChatMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chat_session_id')
                    ->relationship('chatSession', 'id')
                    ->required(),
                TextInput::make('sender_type')
                    ->required()
                    ->default('customer'),
                TextInput::make('sender_id')
                    ->numeric(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
