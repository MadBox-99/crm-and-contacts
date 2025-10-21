<?php

declare(strict_types=1);

namespace App\Filament\Resources\Complaints\Pages;

use App\Filament\Resources\Complaints\ComplaintResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
