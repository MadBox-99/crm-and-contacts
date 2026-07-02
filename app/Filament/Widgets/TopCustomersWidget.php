<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Override;

final class TopCustomersWidget extends TableWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Top Customers by Revenue'))
            ->query(
                Customer::query()
                    ->whereHas('orders')
                    ->withCount('orders')
                    ->withSum('orders', 'total')
                    ->orderByDesc('orders_sum_total')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer')),
                TextColumn::make('orders_count')
                    ->label(__('Orders'))
                    ->alignEnd(),
                TextColumn::make('orders_sum_total')
                    ->label(__('Revenue'))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0).' Ft')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
