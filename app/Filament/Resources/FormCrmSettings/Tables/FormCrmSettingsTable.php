<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class FormCrmSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registrationForm.name')
                    ->label(__('Form'))
                    ->searchable(),
                IconColumn::make('create_opportunity')
                    ->label(__('Opportunity'))
                    ->boolean(),
                TextColumn::make('campaign.name')
                    ->label(__('Campaign'))
                    ->placeholder('—'),
                IconColumn::make('enable_scoring')
                    ->label(__('Scoring'))
                    ->boolean(),
            ])
            ->filters([
                //
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
