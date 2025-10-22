<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\CustomerType;
use App\Filament\Exports\CampaignAudienceExporter;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
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

final class TargetAudienceRelationManager extends RelationManager
{
    protected static string $relationship = 'targetAudience';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Target Audience';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('added_at')
                    ->label('Added At')
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
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('addresses.city')
                    ->label('City')
                    ->searchable()
                    ->toggleable()
                    ->limitedToFirst(),
                TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('pivot.added_at')
                    ->label('Added to Campaign')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pivot.notes')
                    ->label('Campaign Notes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Customer Type')
                    ->options(CustomerType::class),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All customers')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Filter::make('city')
                    ->form([
                        Select::make('city')
                            ->label('City')
                            ->searchable()
                            ->options(function (): array {
                                return \App\Models\CustomerAddress::query()
                                    ->distinct()
                                    ->pluck('city', 'city')
                                    ->filter()
                                    ->toArray();
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['city'],
                            fn (Builder $query, $city): Builder => $query->whereHas(
                                'addresses',
                                fn (Builder $query) => $query->where('city', $city)
                            )
                        );
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Customers')
                    ->modalHeading('Add Customers to Target Audience')
                    ->modalDescription('Select customers to add to this campaign\'s target audience.')
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
}
