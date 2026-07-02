<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\Tables;

use App\Enums\OpportunityStage;
use App\Filament\Resources\LeadOpportunities\LeadOpportunitiesResource;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class LeadOpportunitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('stage', OpportunityStage::Lead))
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('Customer Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.email')
                    ->label(__('Customer Email'))
                    ->searchable(),
                TextColumn::make('customer.phone')
                    ->label(__('Customer Phone'))
                    ->searchable(),
                TextColumn::make('customer.type')
                    ->label(__('Customer Type'))
                    ->badge(),
                TextColumn::make('title')
                    ->label(__('Opportunity Title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('probability')
                    ->label(__('Probability'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('expected_close_date')
                    ->label(__('Expected Close Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned To'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }

    public static function configureDashboard(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stage')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('probability')
                    ->label(__('Probability'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('expected_close_date')
                    ->label(__('Expected Close'))
                    ->date()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned To'))
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record): string => LeadOpportunitiesResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
