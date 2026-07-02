<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\SalesIntegrationInterface;
use App\Events\InvoiceGenerated;

final readonly class PushInvoiceToFinance
{
    public function __construct(
        private SalesIntegrationInterface $integration,
    ) {}

    public function handle(InvoiceGenerated $event): void
    {
        $this->integration->pushInvoiceToFinance($event->invoice);
    }
}
