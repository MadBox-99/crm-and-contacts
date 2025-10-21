<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\Schemas;

use App\Enums\OpportunityStage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

final class LeadOpportunityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->string()
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('value')
                    ->visible(false)
                    ->numeric()
                    ->prefix('HUF')
                    ->required(),
                Slider::make('probability')
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->range(minValue: 0, maxValue: 100)
                    ->tooltips()
                    ->step(5)
                    ->default(10)
                    ->fillTrack()
                    ->pips(PipsMode::Steps, 5),
                Select::make('stage')
                    ->options(OpportunityStage::class)
                    ->default(OpportunityStage::Lead)
                    ->required(),
                DatePicker::make('expected_close_date')
                    ->native(false)
                    ->required(),
                Select::make('assigned_to')
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(Auth::id())
                    ->required(),
            ]);
    }
}
