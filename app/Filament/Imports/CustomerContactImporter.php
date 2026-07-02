<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Filament\Imports\Columns\ImportColumn;
use App\Models\Customer;
use App\Models\CustomerContact;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;
use Override;

final class CustomerContactImporter extends Importer
{
    protected static ?string $model = CustomerContact::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('customer')
                ->requiredMapping()
                ->relationship(resolveUsing: fn (string $state): ?Customer => Customer::query()
                    ->where('unique_identifier', $state)
                    ->orWhere('name', $state)
                    ->orWhere('email', $state)
                    ->orWhere('tax_number', $state)
                    ->first())
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('phone'),
            ImportColumn::make('position'),
            ImportColumn::make('is_primary')
                ->localizedBoolean(default: false),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer contact import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    #[Override]
    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label(__('Update existing records')),
        ];
    }

    #[Override]
    public function resolveRecord(): CustomerContact
    {
        if ($this->options['updateExisting'] ?? false) {
            return CustomerContact::query()->firstOrNew([
                'customer_id' => $this->data['customer_id'] ?? null,
                'name' => $this->data['name'],
            ]);
        }

        return new CustomerContact();
    }

    protected function beforeCreate(): void
    {
        $this->record->team_id = $this->options['teamId'] ?? null;
    }
}
