<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Override;

final class Register extends BaseRegister
{
    public string $view = 'filament.pages.auth.register';

    protected static string $layout = 'filament.layouts.auth-split';

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCompanyFieldset(),
            ])
            ->columns(2);
    }

    #[Override]
    protected function getNameFormComponent(): TextInput
    {
        return TextInput::make('name')
            ->label(__('Full name'))
            ->required()
            ->maxLength(255)
            ->autofocus()
            ->columnSpanFull();
    }

    #[Override]
    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label(__('Work email'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->columnSpanFull();
    }

    #[Override]
    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label(__('Password'))
            ->password()
            ->revealable()
            ->required()
            ->minLength(8)
            ->same('passwordConfirmation')
            ->validationAttribute(__('password'));
    }

    #[Override]
    protected function getPasswordConfirmationFormComponent(): TextInput
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('Confirm password'))
            ->password()
            ->revealable()
            ->required()
            ->dehydrated(false);
    }

    private function getCompanyFieldset(): Fieldset
    {
        return Fieldset::make(__('Company information'))
            ->schema([
                TextInput::make('company_name')
                    ->label(__('Company name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('position')
                    ->label(__('Your position'))
                    ->maxLength(255),
            ])
            ->columns(2)
            ->columnSpanFull();
    }
}
