<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('awardPoints')
                ->label(__('Award Points'))
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->schema([
                    TextInput::make('points')
                        ->label(__('Points'))
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    TextInput::make('description')
                        ->label(__('Description'))
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    /** @var Customer $customer */
                    $customer = $this->record;

                    resolve(LoyaltyService::class)->adjustPoints(
                        customer: $customer,
                        points: (int) $data['points'],
                        description: $data['description'] ?? null,
                    );

                    Notification::make()
                        ->title(__('Points awarded successfully'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['loyalty_points', 'loyalty_level_id']);
                }),
            Action::make('deductPoints')
                ->label(__('Deduct Points'))
                ->icon('heroicon-o-minus-circle')
                ->color('danger')
                ->schema([
                    TextInput::make('points')
                        ->label(__('Points'))
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    TextInput::make('description')
                        ->label(__('Description'))
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    /** @var Customer $customer */
                    $customer = $this->record;

                    resolve(LoyaltyService::class)->adjustPoints(
                        customer: $customer,
                        points: -((int) $data['points']),
                        description: $data['description'] ?? null,
                    );

                    Notification::make()
                        ->title(__('Points deducted successfully'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['loyalty_points', 'loyalty_level_id']);
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
