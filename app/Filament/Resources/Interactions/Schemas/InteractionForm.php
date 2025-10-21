<?php

declare(strict_types=1);

namespace App\Filament\Resources\Interactions\Schemas;

use App\Enums\InteractionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class InteractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('type')
                    ->options(InteractionType::class)
                    ->required()
                    ->default('note'),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('interaction_date')
                    ->required(),
                TextInput::make('duration')
                    ->numeric(),
                TextInput::make('next_action'),
                DatePicker::make('next_action_date'),
            ]);
    }
}
