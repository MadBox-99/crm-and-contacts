<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessions\RelationManagers;

use App\Enums\ChatMessageSenderType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Override;

final class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sender_type')
                    ->label(__('Sender Type'))
                    ->options(ChatMessageSenderType::class)
                    ->required()
                    ->reactive()
                    ->disabled(),
                Select::make('sender_id')
                    ->label(__('Sender'))
                    ->required()
                    ->disabled(),
                Textarea::make('message')
                    ->label(__('Message'))
                    ->required()
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Hidden::make('parent_message_id'),
                Hidden::make('is_read'),
                Hidden::make('read_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('sender_type')
                    ->label(__('From'))
                    ->badge()
                    ->sortable()
                    ->color(fn (ChatMessageSenderType $state): array => match ($state) {
                        ChatMessageSenderType::User => Color::Blue,
                        ChatMessageSenderType::Customer => Color::Green,
                    }),
                TextColumn::make('sender.name')
                    ->label(__('Sender'))
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->sender?->name ?? 'Unknown'),
                TextColumn::make('message')
                    ->label(__('Message'))
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->message),
                IconColumn::make('is_read')
                    ->label(__('Read'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('M d, Y H:i:s')),
                TextColumn::make('read_at')
                    ->label(__('Read At'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->placeholder('Not read')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parentMessage.message')
                    ->label(__('Reply To'))
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sender_type')
                    ->label(__('Sender Type'))
                    ->options(ChatMessageSenderType::class)
                    ->multiple(),
                SelectFilter::make('is_read')
                    ->label(__('Read Status'))
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                    ]),
            ])
            ->defaultSort('created_at', 'asc')
            ->headerActions([
                Action::make('mark_all_read')
                    ->label('Mark All as Read')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire): void {
                        $livewire->getOwnerRecord()
                            ->messages()
                            ->where('is_read', false)
                            ->update(['is_read' => true, 'read_at' => now()]);
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color(Color::Green)
                    ->visible(fn ($record): bool => ! $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true, 'read_at' => now()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('15s');
    }

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Message');
    }
}
