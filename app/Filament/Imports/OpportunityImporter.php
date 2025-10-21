<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\CustomerType;
use App\Enums\OpportunityStage;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

final class OpportunityImporter extends Importer
{
    protected static ?string $model = Opportunity::class;

    public static function getColumns(): array
    {
        return [
            // Customer fields
            ImportColumn::make('customer_unique_identifier')
                ->label('Unique Identifier')
                ->requiredMapping()
                ->rules(['nullable', 'string', 'max:255'])
                ->example('CUST-123456'),

            ImportColumn::make('customer_name')
                ->label('Name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('customer_type')
                ->label('Type')
                ->requiredMapping()
                ->rules(['required', 'in:individual,company'])
                ->example('company'),

            ImportColumn::make('customer_tax_number')
                ->label('Tax Number')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('customer_registration_number')
                ->label('Registration Number')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('customer_email')
                ->label('Email Address')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),

            ImportColumn::make('customer_phone')
                ->label('Phone')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('source')
                ->label('Source')
                ->requiredMapping()
                ->rules(['required', 'in:email,sms,chat,social'])
                ->example('email'),

            // Opportunity fields
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('description')
                ->rules(['nullable', 'string']),

            ImportColumn::make('value')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('probability')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0', 'max:100']),

            ImportColumn::make('stage')
                ->requiredMapping()
                ->rules(['required', 'in:lead,qualified,proposal,negotiation,sended_quotation,lost_quotation'])
                ->example('lead'),

            ImportColumn::make('expected_close_date')
                ->rules(['nullable', 'date']),

            ImportColumn::make('assigned_to')
                ->label('Assigned User Email')
                ->rules(['nullable', 'email', 'exists:users,email']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your opportunity import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    public function resolveRecord(): Opportunity
    {
        // First, try to find existing customer by any of the identifying fields
        $customer = Customer::query()
            ->where(function ($query): void {
                $query->where('unique_identifier', $this->data['customer_unique_identifier'])
                    ->orWhere('name', $this->data['customer_name'])
                    ->orWhere('email', $this->data['customer_email']);

                // Only check tax_number if it's provided
                if (! empty($this->data['customer_tax_number'])) {
                    $query->orWhere('tax_number', $this->data['customer_tax_number']);
                }
            })
            ->first();

        // If customer exists, update it; otherwise create a new one
        if ($customer) {
            $customer->update([
                'unique_identifier' => $this->data['customer_unique_identifier'],
                'name' => $this->data['customer_name'],
                'type' => CustomerType::from($this->data['customer_type']),
                'tax_number' => $this->data['customer_tax_number'] ?? null,
                'registration_number' => $this->data['customer_registration_number'] ?? null,
                'email' => $this->data['customer_email'],
                'phone' => $this->data['customer_phone'] ?? null,
                'is_active' => true,
            ]);
        } else {
            $customer = Customer::query()->create([
                'unique_identifier' => $this->data['customer_unique_identifier'],
                'name' => $this->data['customer_name'],
                'type' => CustomerType::from($this->data['customer_type']),
                'tax_number' => $this->data['customer_tax_number'] ?? null,
                'registration_number' => $this->data['customer_registration_number'] ?? null,
                'email' => $this->data['customer_email'],
                'phone' => $this->data['customer_phone'] ?? null,
                'is_active' => true,
            ]);
        }

        // Create a Communication record to track the source
        Communication::query()->create([
            'customer_id' => $customer->id,
            'channel' => CommunicationChannel::from($this->data['source']),
            'direction' => CommunicationDirection::Inbound,
            'subject' => 'Lead from '.$this->data['source'],
            'content' => 'Customer lead imported from '.$this->data['source'].' source. Opportunity: '.$this->data['title'],
            'status' => CommunicationStatus::Delivered,
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);

        // Resolve assigned user if email is provided
        $assignedUserId = null;
        if (! empty($this->data['assigned_to'])) {
            $assignedUser = User::query()->where('email', $this->data['assigned_to'])->first();
            $assignedUserId = $assignedUser?->id;
        }

        // Create or return opportunity
        return Opportunity::query()->make([
            'customer_id' => $customer->id,
            'title' => $this->data['title'],
            'description' => $this->data['description'] ?? null,
            'value' => $this->data['value'] ?? null,
            'probability' => $this->data['probability'],
            'stage' => OpportunityStage::from($this->data['stage']),
            'expected_close_date' => $this->data['expected_close_date'] ?? null,
            'assigned_to' => $assignedUserId,
        ]);
    }
}
