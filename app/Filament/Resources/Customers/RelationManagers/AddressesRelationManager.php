<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Imports\CustomerAddressImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Addresses');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('Type'))
                    ->required()
                    ->default('billing')
                    ->options([
                        'billing' => __('Billing'),
                        'shipping' => __('Shipping'),
                    ]),
                TextInput::make('country')
                    ->label(__('Country'))
                    ->required(),
                TextInput::make('postal_code')
                    ->label(__('Postal Code'))
                    ->required(),
                TextInput::make('city')
                    ->label(__('City'))
                    ->required(),
                TextInput::make('street')
                    ->label(__('Street'))
                    ->required(),
                TextInput::make('building_number')
                    ->label(__('Building Number')),
                TextInput::make('floor')
                    ->label(__('Floor')),
                TextInput::make('door')
                    ->label(__('Door')),
                Toggle::make('is_default')
                    ->label(__('Default'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                TextColumn::make('country')
                    ->label(__('Country'))
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->label(__('Postal Code'))
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('City'))
                    ->searchable(),
                TextColumn::make('street')
                    ->label(__('Street'))
                    ->searchable(),
                TextColumn::make('building_number')
                    ->label(__('Building Number'))
                    ->searchable(),
                TextColumn::make('floor')
                    ->label(__('Floor'))
                    ->searchable(),
                TextColumn::make('door')
                    ->label(__('Door'))
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean(),
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
                ImportAction::make()
                    ->importer(CustomerAddressImporter::class)
                    ->options([
                        'customerId' => $this->getOwnerRecord()->getKey(),
                        'teamId' => Filament::getTenant()?->getKey(),
                    ]),
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

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Address');
    }
}
