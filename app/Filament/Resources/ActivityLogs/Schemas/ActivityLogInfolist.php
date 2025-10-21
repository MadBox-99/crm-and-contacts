<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('log_name')
                                    ->label('Log Name')
                                    ->badge(),
                                TextEntry::make('event')
                                    ->label('Event')
                                    ->badge()
                                    ->colors([
                                        'success' => 'created',
                                        'warning' => 'updated',
                                        'danger' => 'deleted',
                                    ]),
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Subject Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('subject_type')
                                    ->label('Subject Type')
                                    ->formatStateUsing(fn ($state): string => $state ? class_basename($state) : 'N/A'),
                                TextEntry::make('subject_id')
                                    ->label('Subject ID'),
                            ]),
                    ]),
                Section::make('Causer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('causer_type')
                                    ->label('Causer Type')
                                    ->formatStateUsing(fn ($state): string => $state ? class_basename($state) : 'System'),
                                TextEntry::make('causer_id')
                                    ->label('Causer ID')
                                    ->placeholder('System'),
                                TextEntry::make('causer.name')
                                    ->label('User Name')
                                    ->placeholder('System'),
                                TextEntry::make('causer.email')
                                    ->label('User Email')
                                    ->placeholder('System'),
                            ]),
                    ]),
                Section::make('Changes')
                    ->schema([
                        KeyValueEntry::make('properties.attributes')
                            ->label('New Values')
                            ->columnSpanFull()
                            ->visible(fn ($record): bool => ! empty($record->properties['attributes'] ?? [])),
                        KeyValueEntry::make('properties.old')
                            ->label('Old Values')
                            ->columnSpanFull()
                            ->visible(fn ($record): bool => ! empty($record->properties['old'] ?? [])),
                    ])
                    ->collapsed(),
                Section::make('Metadata')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('batch_uuid')
                                    ->label('Batch UUID')
                                    ->placeholder('N/A'),
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }
}
