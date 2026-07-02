<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\LoyaltyPointSource;
use App\Enums\LoyaltyTransactionType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class LoyaltyPointsRelationManager extends RelationManager
{
    protected static string $relationship = 'loyaltyPointTransactions';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Point History');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('points')
                    ->label(__('Points'))
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger'),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),
                TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50),
                TextColumn::make('balance_after')
                    ->label(__('Balance After'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options(LoyaltyTransactionType::class),
                SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options(LoyaltyPointSource::class),
            ]);
    }
}
