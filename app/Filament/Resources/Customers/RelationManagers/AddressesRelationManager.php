<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->required()
                    ->default('billing')
                    ->options([
                        'billing' => 'Billing',
                        'shipping' => 'Shipping',
                    ]),
                TextInput::make('country')
                    ->required(),
                TextInput::make('postal_code')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('street')
                    ->required(),
                TextInput::make('building_number'),
                TextInput::make('floor'),
                TextInput::make('door'),
                Toggle::make('is_default')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('street')
                    ->searchable(),
                TextColumn::make('building_number')
                    ->searchable(),
                TextColumn::make('floor')
                    ->searchable(),
                TextColumn::make('door')
                    ->searchable(),
                IconColumn::make('is_default')
                    ->boolean(),
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
