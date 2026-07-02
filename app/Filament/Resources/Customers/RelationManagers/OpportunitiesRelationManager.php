<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\OpportunityStage;
use App\Filament\Resources\Customers\Actions\GenerateQuoteAction;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Override;

final class OpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunities';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Opportunities');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->string()
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                TextInput::make('value')
                    ->label(__('Value'))
                    ->numeric(),
                Slider::make('probability')
                    ->label(__('Probability'))
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->range(minValue: 0, maxValue: 100)
                    ->tooltips()
                    ->step(5)
                    ->default(0)
                    ->fillTrack()
                    ->pips(PipsMode::Steps, 5),

                Select::make('stage')
                    ->label(__('Stage'))
                    ->options(OpportunityStage::class)
                    ->default(OpportunityStage::Lead)
                    ->required(),
                DatePicker::make('expected_close_date')
                    ->label(__('Expected Close Date')),
                Select::make('assigned_to')
                    ->label(__('Assigned User'))
                    ->relationship('assignedUser', 'name', modifyQueryUsing: fn ($query) => $query->whereRelation('teams', 'teams.id', resolve('current_team')->getKey()))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(Auth::id())
                    ->required(),
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
                TextColumn::make('value')
                    ->label(__('Value'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('probability')
                    ->label(__('Probability'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stage')
                    ->label(__('Stage'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('expected_close_date')
                    ->label(__('Expected Close Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('Assigned User'))
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
                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                GenerateQuoteAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }

    #[Override]
    protected static function getModelLabel(): string
    {
        return __('Opportunity');
    }
}
