<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class CommunicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Communications');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('channel')
                    ->label(__('Channel'))
                    ->options(CommunicationChannel::class)
                    ->default(CommunicationChannel::Email)
                    ->required(),
                Select::make('direction')
                    ->label(__('Direction'))
                    ->options(CommunicationDirection::class)
                    ->default(CommunicationDirection::Outbound)
                    ->required(),
                TextInput::make('subject')
                    ->label(__('Subject')),
                Textarea::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(CommunicationStatus::class)
                    ->default(CommunicationStatus::Pending)
                    ->required(),
                DateTimePicker::make('sent_at')
                    ->label(__('Sent At')),
                DateTimePicker::make('delivered_at')
                    ->label(__('Delivered At')),
                DateTimePicker::make('read_at')
                    ->label(__('Read At')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                TextColumn::make('channel')
                    ->label(__('Channel'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('direction')
                    ->label(__('Direction'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->label(__('Sent At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delivered_at')
                    ->label(__('Delivered At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('read_at')
                    ->label(__('Read At'))
                    ->dateTime()
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

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Communication');
    }
}
