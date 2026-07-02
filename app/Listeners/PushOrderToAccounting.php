<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\SalesIntegrationInterface;
use App\Events\OrderCreated;

final readonly class PushOrderToAccounting
{
    public function __construct(
        private SalesIntegrationInterface $integration,
    ) {}

    public function handle(OrderCreated $event): void
    {
        $this->integration->pushOrderToAccounting($event->order);
    }
}
