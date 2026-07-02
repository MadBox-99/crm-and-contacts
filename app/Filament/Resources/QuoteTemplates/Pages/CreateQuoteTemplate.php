<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteTemplates\Pages;

use App\Filament\Resources\QuoteTemplates\QuoteTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Override;

final class CreateQuoteTemplate extends CreateRecord
{
    protected static string $resource = QuoteTemplateResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }
}
