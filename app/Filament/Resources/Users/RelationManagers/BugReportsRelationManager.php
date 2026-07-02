<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\BugReportStatus;
use App\Enums\ComplaintSeverity;
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
use Override;

final class BugReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'bugReports';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->options(BugReportStatus::class)
                    ->default(BugReportStatus::Open)
                    ->required(),
                Select::make('assigned_to')
                    ->label(__('Assigned User'))
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('resolved_at')
                    ->label(__('Resolved At')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('severity')
                    ->label(__('Severity'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned User'))
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

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Bug Report');
    }
}
