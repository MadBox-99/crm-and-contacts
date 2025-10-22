<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shipments\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TrackingEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'trackingEvents';

    protected static ?string $title = 'Tracking Events Timeline';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status_code')
                    ->label('Status Code')
                    ->placeholder('IN_TRANSIT, DELIVERED, etc.')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->label('Location')
                    ->placeholder('Budapest, Hungary')
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Package is in transit...')
                    ->rows(3)
                    ->columnSpanFull(),

                DateTimePicker::make('occurred_at')
                    ->label('Occurred At')
                    ->required()
                    ->default(now()),

                KeyValue::make('metadata')
                    ->label('Additional Data')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->reorderable(false)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status_code')
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Time')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->occurred_at->diffForHumans()),

                TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->icon('heroicon-o-map-pin')
                    ->placeholder('—'),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
