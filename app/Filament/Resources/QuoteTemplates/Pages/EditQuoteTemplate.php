<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteTemplates\Pages;

use App\Filament\Resources\QuoteTemplates\QuoteTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditQuoteTemplate extends EditRecord
{
    protected static string $resource = QuoteTemplateResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
