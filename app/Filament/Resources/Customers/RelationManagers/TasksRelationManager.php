<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Override;

final class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Tasks');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assigned_to')
                    ->label(__('Assigned User'))
                    ->relationship('assignedUser', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                    ->required(),
                Select::make('assigned_by')
                    ->label(__('Assigned By'))
                    ->relationship('assigner', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                    ->default(Auth::id()),
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                Select::make('priority')
                    ->label(__('Priority'))
                    ->options([
                        'low' => __('Low'),
                        'medium' => __('Medium'),
                        'high' => __('High'),
                        'urgent' => __('Urgent'),
                    ])
                    ->default('medium')
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'in_progress' => __('In Progress'),
                        'completed' => __('Completed'),
                        'cancelled' => __('Cancelled'),
                    ])
                    ->default('pending')
                    ->required(),
                DatePicker::make('due_date')
                    ->label(__('Due Date')),
                DateTimePicker::make('completed_at')
                    ->label(__('Completed At')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned User'))
                    ->sortable(),
                TextColumn::make('assigner.name')
                    ->label(__('Assigned By'))
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('priority')
                    ->label(__('Priority'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('Completed At'))
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
}
