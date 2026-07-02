<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\InteractionType;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

final class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(InteractionType::class)
                    ->default(InteractionType::Note)
                    ->required(),
                TextInput::make('subject')
                    ->label(__('Subject'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                DateTimePicker::make('interaction_date')
                    ->label(__('Interaction Date'))
                    ->required(),
                TextInput::make('duration')
                    ->label(__('Duration'))
                    ->numeric(),
                TextInput::make('next_action')
                    ->label(__('Next Action')),
                DatePicker::make('next_action_date')
                    ->label(__('Next Action Date')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable(),
                TextColumn::make('interaction_date')
                    ->label(__('Interaction Date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('Duration'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('next_action')
                    ->label(__('Next Action'))
                    ->searchable(),
                TextColumn::make('next_action_date')
                    ->label(__('Next Action Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
