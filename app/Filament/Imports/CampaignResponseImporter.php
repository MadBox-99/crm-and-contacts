<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\CampaignResponse;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;

final class CampaignResponseImporter extends Importer
{
    protected static ?string $model = CampaignResponse::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('campaign')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('customer')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('response_type')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('notes'),
            ImportColumn::make('responded_at')
                ->rules(['datetime']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your campaign response import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

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

    public function resolveRecord(): CampaignResponse
    {

        if ($this->options['updateExisting'] ?? false) {
            return CampaignResponse::query()->firstOrNew([
                'id' => $this->data['id'],
            ]);
        }

        return new CampaignResponse();
    }
}
