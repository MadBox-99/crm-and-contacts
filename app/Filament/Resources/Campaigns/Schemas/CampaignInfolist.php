<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Campaign Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->weight('bold'),
                                TextEntry::make('campaign_type')
                                    ->badge(),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('creator.name')
                                    ->label('Created By')
                                    ->placeholder('-'),
                            ]),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Campaign Period')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_date')
                                    ->date(),
                                TextEntry::make('end_date')
                                    ->date()
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Budget & Costs')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('budget')
                                    ->money('HUF')
                                    ->placeholder('-'),
                                TextEntry::make('actual_cost')
                                    ->money('HUF'),
                                TextEntry::make('budget_usage')
                                    ->label('Budget Usage')
                                    ->formatStateUsing(fn (Campaign $record): string => $record->getBudgetUsagePercentage().'%')
                                    ->badge()
                                    ->color(fn (Campaign $record): string => match (true) {
                                        $record->getBudgetUsagePercentage() >= 100 => 'danger',
                                        $record->getBudgetUsagePercentage() >= 80 => 'warning',
                                        default => 'success',
                                    }),
                            ]),
                    ]),

                Section::make('Performance Metrics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('impressions')
                                    ->numeric()
                                    ->icon('heroicon-o-eye'),
                                TextEntry::make('clicks')
                                    ->numeric()
                                    ->icon('heroicon-o-cursor-arrow-rays'),
                                TextEntry::make('conversions_count')
                                    ->label('Conversions')
                                    ->formatStateUsing(fn (Campaign $record): int => $record->getConversionCount())
                                    ->icon('heroicon-o-check-circle'),
                                TextEntry::make('total_revenue')
                                    ->label('Total Revenue')
                                    ->formatStateUsing(fn (Campaign $record): string => number_format($record->getTotalConversionValue(), 0).' Ft')
                                    ->icon('heroicon-o-currency-dollar'),
                            ]),
                    ]),

                Section::make('KPI Metrics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('cpc')
                                    ->label('Cost Per Conversion')
                                    ->formatStateUsing(fn (Campaign $record): string => $record->getCostPerConversion() !== null ? number_format($record->getCostPerConversion(), 0).' Ft' : '-')
                                    ->icon('heroicon-o-banknotes'),
                                TextEntry::make('conversion_rate')
                                    ->label('Conversion Rate')
                                    ->formatStateUsing(fn (Campaign $record): string => $record->getConversionRate() !== null ? $record->getConversionRate().'%' : '-')
                                    ->icon('heroicon-o-chart-bar'),
                                TextEntry::make('roi')
                                    ->label('ROI')
                                    ->formatStateUsing(fn (Campaign $record): string => $record->getROI() !== null ? $record->getROI().'%' : '-')
                                    ->badge()
                                    ->color(fn (Campaign $record): string|array => match (true) {
                                        $record->getROI() === null => 'gray',
                                        $record->getROI() >= 100 => 'success',
                                        $record->getROI() >= 0 => 'warning',
                                        default => 'danger',
                                    })
                                    ->icon('heroicon-o-arrow-trending-up'),
                                TextEntry::make('roas')
                                    ->label('ROAS')
                                    ->formatStateUsing(fn (Campaign $record): string => $record->getROAS() !== null ? number_format($record->getROAS(), 2) : '-')
                                    ->badge()
                                    ->color(fn (Campaign $record): string|array => match (true) {
                                        $record->getROAS() === null => 'gray',
                                        $record->getROAS() >= 3 => 'success',
                                        $record->getROAS() >= 1 => 'warning',
                                        default => 'danger',
                                    })
                                    ->icon('heroicon-o-arrow-up-circle'),
                            ]),
                    ]),

                Section::make('Google Ads Integration')
                    ->schema([
                        TextEntry::make('google_ads_campaign_id')
                            ->label('Google Ads Campaign ID')
                            ->placeholder('-')
                            ->copyable()
                            ->icon('heroicon-o-link'),
                    ])
                    ->visible(fn (Campaign $record): bool => $record->google_ads_campaign_id !== null),

                Section::make('Target Audience')
                    ->schema([
                        TextEntry::make('target_audience_criteria')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('System Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->dateTime(),
                                TextEntry::make('deleted_at')
                                    ->dateTime()
                                    ->visible(fn (Campaign $record): bool => $record->trashed()),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
