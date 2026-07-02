<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
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

final class ComplaintsRelationManager extends RelationManager
{
    protected static string $relationship = 'complaints';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Complaints');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label(__('Order'))
                    ->relationship('order', 'order_number')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('#%s — %s', $record->order_number, $record->customer?->name)),
                Select::make('reported_by')
                    ->label(__('Reported By'))
                    ->relationship('reporter', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey())),
                Select::make('assigned_to')
                    ->label(__('Assigned User'))
                    ->relationship('assignedUser', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey())),
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
                    ->default(ComplaintSeverity::Medium)
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(ComplaintStatus::class)
                    ->default(ComplaintStatus::Open)
                    ->required(),
                Textarea::make('resolution')
                    ->label(__('Resolution'))
                    ->columnSpanFull(),
                DateTimePicker::make('reported_at')
                    ->label(__('Reported At'))
                    ->required(),
                DateTimePicker::make('resolved_at')
                    ->label(__('Resolved At')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('order.order_number')
                    ->label(__('Order'))
                    ->searchable(),
                TextColumn::make('reporter.name')
                    ->label(__('Reported By'))
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned User'))
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('severity')
                    ->label(__('Severity'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
                TextColumn::make('reported_at')
                    ->label(__('Reported At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('resolved_at')
                    ->label(__('Resolved At'))
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
