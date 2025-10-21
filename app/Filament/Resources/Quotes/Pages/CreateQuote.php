<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;
}
