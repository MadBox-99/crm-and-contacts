<?php

declare(strict_types=1);

namespace App\Filament\Resources\BugReports\Pages;

use App\Filament\Resources\BugReports\BugReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListBugReports extends ListRecords
{
    protected static string $resource = BugReportResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
