<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\ChatSessionStatus;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

final class ChatSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'chatSessions';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name'),
                DateTimePicker::make('started_at')
                    ->label(__('Started At'))
                    ->required(),
                DateTimePicker::make('ended_at')
                    ->label(__('Ended At')),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(ChatSessionStatus::class)
                    ->default(ChatSessionStatus::Active)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('started_at')
            ->columns([
                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(__('Started At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->label(__('Ended At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
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

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Chat Session');
    }
}
