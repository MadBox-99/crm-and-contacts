<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProposalOpportunities\Tables;

use App\Enums\OpportunityStage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProposalOpportunitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('stage', OpportunityStage::Proposal))
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.email')
                    ->label('Customer Email')
                    ->searchable(),
                TextColumn::make('customer.phone')
                    ->label('Customer Phone')
                    ->searchable(),
                TextColumn::make('customer.type')
                    ->label('Customer Type')
                    ->badge(),
                TextColumn::make('title')
                    ->label('Opportunity Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Value')
                    ->money('HUF')
                    ->sortable(),
                TextColumn::make('probability')
                    ->label('Probability')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('expected_close_date')
                    ->label('Expected Close Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
