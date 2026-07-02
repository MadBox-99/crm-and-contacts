<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('complaint_number')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('order_id')
                    ->label(__('Order'))
                    ->relationship('order', 'order_number')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->order_number, $record->customer?->name))
                    ->searchable()
                    ->preload(),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(ComplaintType::class)
                    ->enum(ComplaintType::class),
                TextInput::make('subject')
                    ->label(__('Subject')),
                Select::make('reported_by')
                    ->label(__('Reporter'))
                    ->relationship('reporter', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('assigned_to')
                    ->label(__('Assigned User'))
                    ->relationship('assignedUser', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->required()
                    ->columnSpanFull(),
                Select::make('severity')
                    ->label(__('Severity'))
                    ->options(ComplaintSeverity::class)
                    ->enum(ComplaintSeverity::class)
                    ->required()
                    ->default(ComplaintSeverity::Medium),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(ComplaintStatus::class)
                    ->enum(ComplaintStatus::class)
                    ->required()
                    ->default(ComplaintStatus::Open),
                TextInput::make('escalation_level')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                Textarea::make('resolution')
                    ->label(__('Resolution'))
                    ->columnSpanFull(),
                DateTimePicker::make('reported_at')
                    ->label(__('Reported at'))
                    ->required(),
                DateTimePicker::make('sla_deadline_at')
                    ->label(__('SLA Deadline'))
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                DateTimePicker::make('resolved_at')
                    ->label(__('Resolved at')),
            ]);
    }
}
