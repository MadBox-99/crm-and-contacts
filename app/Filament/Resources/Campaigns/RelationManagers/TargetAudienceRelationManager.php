<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\CustomerType;
use App\Filament\Exports\CampaignAudienceExporter;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Override;

final class TargetAudienceRelationManager extends RelationManager
{
    protected static string $relationship = 'targetAudience';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = null;

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('added_at')
                    ->label(__('Added At'))
                    ->default(now())
                    ->seconds(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('addresses.city')
                    ->label(__('City'))
                    ->searchable()
                    ->toggleable()
                    ->limitedToFirst(),
                TextColumn::make('is_active')
                    ->label(__('Active'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('pivot.added_at')
                    ->label(__('Added to Campaign'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pivot.notes')
                    ->label(__('Campaign Notes'))
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Customer Type'))
                    ->options(CustomerType::class),
                TernaryFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->placeholder('All customers')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Filter::make('city')
                    ->form([
                        Select::make('city')
                            ->label(__('City'))
                            ->searchable()
                            ->options(fn (): array => CustomerAddress::query()
                                ->distinct()
                                ->pluck('city', 'city')
                                ->filter()
                                ->toArray()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['city'],
                        fn (Builder $query, $city): Builder => $query->whereHas(
                            'addresses',
                            fn (Builder $query) => $query->where('city', $city)
                        )
                    )),
                Filter::make('industry')
                    ->form([
                        Select::make('industry')
                            ->label(__('Industry'))
                            ->searchable()
                            ->options(fn (): array => Customer::query()
                                ->whereNotNull('industry')
                                ->distinct()
                                ->pluck('industry', 'industry')
                                ->toArray()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['industry'],
                        fn (Builder $query, $industry): Builder => $query->where('industry', $industry)
                    )),
                Filter::make('last_purchase')
                    ->form([
                        DatePicker::make('purchased_since')
                            ->label(__('Purchased since')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['purchased_since'],
                        fn (Builder $query, $date): Builder => $query->whereHas(
                            'orders',
                            fn (Builder $query) => $query->where('order_date', '>=', $date)
                        )
                    )),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Customers')
                    ->modalHeading('Add Customers to Target Audience')
                    ->modalDescription("Select customers to add to this campaign's target audience.")
                    ->multiple()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email', 'phone'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Select Customers')
                            ->placeholder('Search for customers by name, email, or phone')
                            ->helperText('You can select multiple customers at once.'),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Optional notes about why these customers were selected.'),
                    ])
                    ->after(function (AttachAction $action, array $data): void {
                        // Set added_by for all attached records
                        $records = $action->getRecord()->targetAudience()
                            ->whereIn('customer_id', array_keys($data))
                            ->get();

                        foreach ($records as $record) {
                            $record->pivot->update([
                                'added_by' => Auth::id(),
                                'added_at' => now(),
                            ]);
                        }
                    }),
                ExportAction::make()
                    ->label('Export Audience')
                    ->exporter(CampaignAudienceExporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(CampaignAudienceExporter::class),
                    DetachBulkAction::make()
                        ->label('Remove Selected'),
                ]),
            ])
            ->emptyStateHeading('No target audience selected')
            ->emptyStateDescription('Start building your target audience by adding customers to this campaign.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Target Customer');
    }
}
