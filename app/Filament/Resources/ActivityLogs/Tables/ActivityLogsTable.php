<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

final class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject Type')
                    ->searchable()
                    ->formatStateUsing(fn ($state): string => class_basename($state))
                    ->sortable(),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System'),
                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log Name')
                    ->options(fn () => Activity::query()
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->toArray()
                    ),
                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('subject_type')
                    ->label('Subject Type')
                    ->options(fn () => Activity::query()
                        ->distinct()
                        ->pluck('subject_type', 'subject_type')
                        ->mapWithKeys(fn ($item): array => [$item => class_basename($item)])
                        ->all()
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
