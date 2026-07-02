<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteTemplates\Pages;

use App\Filament\Resources\QuoteTemplates\QuoteTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListQuoteTemplates extends ListRecords
{
    protected static string $resource = QuoteTemplateResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
