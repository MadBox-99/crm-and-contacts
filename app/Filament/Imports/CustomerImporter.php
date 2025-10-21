<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\CustomerType;
use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;

final class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('unique_identifier')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->examples(CustomerType::cases())
                ->rules(['required']),
            ImportColumn::make('tax_number'),
            ImportColumn::make('registration_number'),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('phone'),
            ImportColumn::make('notes'),
            ImportColumn::make('is_active')
                ->requiredMappingForNewRecordsOnly()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Update existing records'),
        ];
    }

    public function resolveRecord(): Customer
    {

        if ($this->options['updateExisting'] ?? false) {
            return Customer::query()->firstOrNew([
                'unique_identifier' => $this->data['unique_identifier'],
            ]);
        }

        return new Customer();
    }
}
