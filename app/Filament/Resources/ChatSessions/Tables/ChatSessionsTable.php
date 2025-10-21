<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessions\Tables;

use App\Enums\ChatSessionStatus;
use App\Models\User;
use App\Services\ChatService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class ChatSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->customer?->email),
                TextColumn::make('user.name')
                    ->label('Assigned Agent')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unassigned')
                    ->color(fn ($state): ?array => $state ? null : Color::Orange),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): array => match ($state) {
                        'urgent' => Color::Red,
                        'high' => Color::Orange,
                        'normal' => Color::Gray,
                        'low' => Color::Blue,
                        default => Color::Gray,
                    }),
                TextColumn::make('unread_count')
                    ->label('Unread')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): array => $state > 0 ? Color::Red : Color::Gray),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => $state ? $state.'/5 ⭐' : '-')
                    ->toggleable(),
                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->started_at?->format('M d, Y H:i:s')),
                TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->last_message_at?->format('M d, Y H:i:s'))
                    ->placeholder('No messages'),
                TextColumn::make('ended_at')
                    ->label('Ended')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ChatSessionStatus::class)
                    ->multiple(),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->multiple(),
                SelectFilter::make('user_id')
                    ->label('Assigned Agent')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('assignment')
                    ->label('Assignment')
                    ->options([
                        'assigned' => 'Assigned',
                        'unassigned' => 'Unassigned',
                    ])
                    ->query(function ($query, array $data) {
                        if (! isset($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'assigned' => $query->whereNotNull('user_id'),
                            'unassigned' => $query->whereNull('user_id'),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('started_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('assign')
                    ->label('Assign to Me')
                    ->icon('heroicon-o-user-plus')
                    ->color(Color::Blue)
                    ->visible(fn ($record): bool => $record->user_id === null)
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $chatService = app(ChatService::class);
                        $chatService->assignSession($record, Auth::user());

                        Notification::make()
                            ->title('Session Assigned')
                            ->success()
                            ->send();
                    }),
                Action::make('transfer')
                    ->label('Transfer')
                    ->icon('heroicon-o-arrow-path')
                    ->color(Color::Orange)
                    ->visible(fn ($record): bool => $record->user_id !== null && $record->status === ChatSessionStatus::Active)
                    ->form([
                        Select::make('new_user_id')
                            ->label('Transfer to Agent')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $chatService = app(ChatService::class);
                        $newUser = User::query()->find($data['new_user_id']);
                        $chatService->transferSession($record, $newUser);

                        Notification::make()
                            ->title('Session Transferred')
                            ->success()
                            ->send();
                    }),
                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color(Color::Red)
                    ->visible(fn ($record): bool => $record->status === ChatSessionStatus::Active)
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to close this chat session?')
                    ->action(function ($record): void {
                        $chatService = app(ChatService::class);
                        $chatService->closeSession($record);

                        Notification::make()
                            ->title('Session Closed')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('30s');
    }
}
