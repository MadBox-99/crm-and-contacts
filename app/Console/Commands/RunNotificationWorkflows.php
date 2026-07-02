<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotificationWorkflowService;
use Illuminate\Console\Command;

final class RunNotificationWorkflows extends Command
{
    protected $signature = 'notifications:run-workflows';

    protected $description = 'Run all notification workflow checks (expiring quotes, deadlines, inactive leads, SLA breaches)';

    public function handle(NotificationWorkflowService $service): int
    {
        $this->info('Running notification workflows...');

        $results = $service->runAll();

        $this->info('Expiring quotes notified: '.$results['quotes']);
        $this->info('Approaching task deadlines notified: '.$results['tasks']);
        $this->info('Inactive leads notified: '.$results['leads']);
        $this->info('SLA breaches notified: '.$results['complaints']);

        return self::SUCCESS;
    }
}
