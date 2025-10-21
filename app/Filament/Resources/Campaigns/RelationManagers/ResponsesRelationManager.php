<?php

declare(strict_types=1);

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\CampaignResponseType;
use App\Filament\Imports\CampaignResponseImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required()
                    ->searchable(),
                Select::make('response_type')
                    ->options(CampaignResponseType::class)
                    ->required()
                    ->default(CampaignResponseType::NoResponse),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('responded_at')
                    ->seconds(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer.name')
            ->columns([
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('response_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('responded_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make('Import Responses')->importer(CampaignResponseImporter::class),
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
