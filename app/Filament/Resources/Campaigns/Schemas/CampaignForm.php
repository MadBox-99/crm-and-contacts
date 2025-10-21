<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Enums\CampaignStatus;
use App\Enums\Role;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

final class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Select::make('status')
                    ->required()
                    ->options(CampaignStatus::class)
                    ->enum(CampaignStatus::class)
                    ->default(CampaignStatus::Draft),
                Textarea::make('target_audience_criteria')
                    ->columnSpanFull(),
                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->default(Auth::user()->id)
                    ->visible(fn () => Auth::user()->hasRole(Role::Admin))
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}
