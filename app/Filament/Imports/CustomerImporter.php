<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\CustomerType;
use App\Filament\Imports\Columns\ImportColumn;
use App\Models\Customer;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Override;

final class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('unique_identifier'),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('type')
                ->examples(CustomerType::cases()),
            ImportColumn::make('tax_number'),
            ImportColumn::make('eu_tax_number'),
            ImportColumn::make('registration_number'),
            ImportColumn::make('industry'),
            ImportColumn::make('website')
                ->rules(['url']),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('phone'),
            ImportColumn::make('notes'),
            ImportColumn::make('is_active')
                ->localizedBoolean(default: true),
            ImportColumn::make('loyalty_points')
                ->numeric()
                ->rules(['integer', 'min:0']),
            ImportColumn::make('loyaltyLevel')
                ->relationship(resolveUsing: 'name'),
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

    #[Override]
    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label(__('Update existing records')),
        ];
    }

    #[Override]
    public function resolveRecord(): Customer
    {
        if (empty($this->data['unique_identifier'])) {
            $this->data['unique_identifier'] = 'CUST-'.Str::upper(Str::random(8));
        }

        if (empty($this->data['type'])) {
            $this->data['type'] = empty($this->data['eu_tax_number'])
                ? CustomerType::Individual->value
                : CustomerType::Company->value;
        }

        if ($this->options['updateExisting'] ?? false) {
            $customer = Customer::query()
                ->where(function ($query): void {
                    $query->where('unique_identifier', $this->data['unique_identifier']);

                    if (! empty($this->data['email'])) {
                        $query->orWhere('email', $this->data['email']);
                    }

                    if (! empty($this->data['tax_number'])) {
                        $query->orWhere('tax_number', $this->data['tax_number']);
                    }
                })
                ->first();

            if (! $customer) {
                throw new RowImportFailedException(sprintf('Nem talalhato ugyfel: [%s].', $this->data['unique_identifier']));
            }

            return $customer;
        }

        return new Customer();
    }

    protected function beforeCreate(): void
    {
        $this->record->team_id = $this->options['teamId'] ?? null;

        if (blank($this->record->unique_identifier)) {
            $this->record->unique_identifier = $this->data['unique_identifier'];
        }

        if (blank($this->record->type)) {
            $this->record->type = $this->data['type'];
        }

        if ($this->record->is_active === null) {
            $this->record->is_active = true;
        }
    }
}
