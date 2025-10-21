<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadOpportunities\RelationManagers;

use App\Enums\QuoteStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('opportunity_id')
                    ->default($this->ownerRecord->id)
                    ->required(),
                Hidden::make('customer_id')
                    ->default($this->ownerRecord->customer_id)
                    ->required(),
                TextInput::make('quote_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn (): string => 'QUO-'.now()->year.'-'.mb_str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT)),
                DatePicker::make('issue_date')
                    ->required()
                    ->default(now())
                    ->native(false),
                DatePicker::make('valid_until')
                    ->required()
                    ->default(now()->addDays(30))
                    ->native(false),
                Select::make('status')
                    ->options(QuoteStatus::class)
                    ->required()
                    ->default(QuoteStatus::Draft),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('HUF')
                    ->default(0),
                TextInput::make('discount_amount')
                    ->numeric()
                    ->prefix('HUF')
                    ->default(0),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->prefix('HUF')
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('HUF')
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quote_number')
            ->columns([
                TextColumn::make('quote_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->date()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->money('HUF')
                    ->sortable(),
                TextColumn::make('total')
                    ->money('HUF')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
