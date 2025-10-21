<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\Tables;

use App\Enums\CampaignType;
use App\Models\Campaign;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('campaign_type')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('budget')
                    ->money('HUF')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('actual_cost')
                    ->money('HUF')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('budget_usage')
                    ->label('Budget Usage')
                    ->formatStateUsing(fn (Campaign $record): string => $record->getBudgetUsagePercentage().'%')
                    ->color(fn (Campaign $record): string => match (true) {
                        $record->getBudgetUsagePercentage() >= 100 => 'danger',
                        $record->getBudgetUsagePercentage() >= 80 => 'warning',
                        default => 'success',
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('actual_cost', $direction))
                    ->toggleable(),
                TextColumn::make('conversions_count')
                    ->label('Conversions')
                    ->counts('conversions')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cpc')
                    ->label('CPC')
                    ->formatStateUsing(fn (Campaign $record): string => $record->getCostPerConversion() !== null ? number_format($record->getCostPerConversion(), 0).' Ft' : '-')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->withCount('conversions')->orderByRaw('CASE WHEN conversions_count = 0 THEN NULL ELSE actual_cost / conversions_count END '.$direction))
                    ->toggleable(),
                TextColumn::make('conversion_rate')
                    ->label('Conv. Rate')
                    ->formatStateUsing(fn (Campaign $record): string => $record->getConversionRate() !== null ? $record->getConversionRate().'%' : '-')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('clicks', $direction))
                    ->toggleable(),
                TextColumn::make('roi')
                    ->label('ROI')
                    ->formatStateUsing(fn (Campaign $record): string => $record->getROI() !== null ? $record->getROI().'%' : '-')
                    ->color(fn (Campaign $record): string => match (true) {
                        $record->getROI() === null => 'gray',
                        $record->getROI() >= 100 => 'success',
                        $record->getROI() >= 0 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(),
                TextColumn::make('roas')
                    ->label('ROAS')
                    ->formatStateUsing(fn (Campaign $record): string => $record->getROAS() !== null ? number_format($record->getROAS(), 2) : '-')
                    ->color(fn (Campaign $record): string => match (true) {
                        $record->getROAS() === null => 'gray',
                        $record->getROAS() >= 3 => 'success',
                        $record->getROAS() >= 1 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(),
                TextColumn::make('clicks')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('impressions')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('campaign_type')
                    ->options(CampaignType::class),
                SelectFilter::make('status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
