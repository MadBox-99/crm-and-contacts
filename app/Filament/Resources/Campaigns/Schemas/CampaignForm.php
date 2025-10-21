<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\Role;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

final class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Campaign Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('campaign_type')
                                    ->required()
                                    ->options(CampaignType::class)
                                    ->enum(CampaignType::class)
                                    ->default(CampaignType::Other)
                                    ->native(false),
                                Select::make('status')
                                    ->required()
                                    ->options(CampaignStatus::class)
                                    ->enum(CampaignStatus::class)
                                    ->default(CampaignStatus::Draft)
                                    ->native(false),
                                DatePicker::make('start_date')
                                    ->required()
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Budget & Performance')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('budget')
                                    ->numeric()
                                    ->prefix('Ft')
                                    ->minValue(0)
                                    ->step(100),
                                TextInput::make('actual_cost')
                                    ->numeric()
                                    ->prefix('Ft')
                                    ->minValue(0)
                                    ->default(0)
                                    ->step(100),
                                TextInput::make('impressions')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                                TextInput::make('clicks')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                            ]),
                    ]),

                Section::make('Google Ads Integration')
                    ->schema([
                        TextInput::make('google_ads_campaign_id')
                            ->label('Google Ads Campaign ID')
                            ->maxLength(255)
                            ->placeholder('Enter Google Ads Campaign ID'),
                    ])
                    ->visible(fn ($get) => $get('campaign_type') === CampaignType::GoogleAds->value)
                    ->collapsed(),

                Section::make('Targeting')
                    ->schema([
                        Textarea::make('target_audience_criteria')
                            ->label('Target Audience Criteria')
                            ->rows(4)
                            ->placeholder('Describe your target audience criteria...')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Section::make('Administration')
                    ->schema([
                        Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->default(Auth::user()->id)
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->visible(fn () => Auth::user()->hasRole(Role::Admin))
                    ->collapsed(),
            ]);
    }
}
