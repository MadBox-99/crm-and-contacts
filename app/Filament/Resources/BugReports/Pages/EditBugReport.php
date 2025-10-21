<?php

declare(strict_types=1);

namespace App\Filament\Resources\BugReports\Pages;

use App\Filament\Resources\BugReports\BugReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditBugReport extends EditRecord
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
