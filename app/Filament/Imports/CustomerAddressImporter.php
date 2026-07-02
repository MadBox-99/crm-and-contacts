<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Filament\Imports\Columns\ImportColumn;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;
use Override;

final class CustomerAddressImporter extends Importer
{
    protected static ?string $model = CustomerAddress::class;

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
            ImportColumn::make('type')
                ->examples(['billing', 'shipping']),
            ImportColumn::make('country')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('postal_code')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('city')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('street')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('building_number'),
            ImportColumn::make('floor'),
            ImportColumn::make('door'),
            ImportColumn::make('is_default')
                ->localizedBoolean(default: false),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer address import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

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
    public function resolveRecord(): CustomerAddress
    {
        if (empty($this->data['type'])) {
            $this->data['type'] = 'billing';
        }

        if ($this->options['updateExisting'] ?? false) {
            return CustomerAddress::query()->firstOrNew([
                'customer_id' => $this->data['customer_id'] ?? null,
                'type' => $this->data['type'],
            ]);
        }

        return new CustomerAddress();
    }

    protected function beforeCreate(): void
    {
        $this->record->team_id = $this->options['teamId'] ?? null;
    }
}
