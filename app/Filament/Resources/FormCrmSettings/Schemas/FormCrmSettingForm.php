<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormCrmSettings\Schemas;

use App\Enums\OpportunityStage;
use Filament\Facades\Filament;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

final class FormCrmSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('registration_form_id')
                ->label(__('Form'))
                ->relationship(
                    'registrationForm',
                    'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query->where('team_id', Filament::getTenant()?->getKey()),
                )
                ->required()
                ->unique(ignoreRecord: true)
                ->searchable(),
            KeyValue::make('field_map')
                ->label(__('Field mapping (CRM field → form field key)'))
                ->keyLabel(__('CRM field'))
                ->valueLabel(__('Form field key'))
                ->helperText(__('Leave empty to auto-detect. Keys: email, name, phone, companyName')),
            Toggle::make('create_opportunity')
                ->label(__('Create opportunity'))
                ->default(true),
            Select::make('opportunity_stage')
                ->label(__('Opportunity stage'))
                ->options(OpportunityStage::class)
                ->default(OpportunityStage::Lead->value),
            Select::make('campaign_id')
                ->label(__('Campaign'))
                ->relationship('campaign', 'name')
                ->searchable(),
            Toggle::make('enable_scoring')
                ->label(__('Enable lead scoring'))
                ->default(true),
        ]);
    }
}
