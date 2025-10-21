<?php

declare(strict_types=1);

namespace App\Filament\Resources\BugReports\Pages;

use App\Filament\Resources\BugReports\BugReportResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateBugReport extends CreateRecord
{
    protected static string $resource = BugReportResource::class;
}
