<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
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
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('order_id')
                    ->relationship('order', 'order_number'),
                Select::make('reported_by')
                    ->relationship('reporter', 'name')
                    ->nullable(),
                Select::make('assigned_to')
                    ->relationship('assignedUser', 'name')
                    ->nullable(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('severity')
                    ->options(ComplaintSeverity::class)
                    ->enum(ComplaintSeverity::class)
                    ->required()
                    ->default(ComplaintSeverity::Medium),
                Select::make('status')
                    ->options(ComplaintStatus::class)
                    ->enum(ComplaintStatus::class)
                    ->required()
                    ->default(ComplaintStatus::Open),
                Textarea::make('resolution')
                    ->columnSpanFull(),
                DateTimePicker::make('reported_at')
                    ->required(),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
