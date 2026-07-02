<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\LoyaltyPointSource;
use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Services\LoyaltyService;

final readonly class AwardLoyaltyPointsOnDelivery
{
    public function __construct(
        private LoyaltyService $loyaltyService,
    ) {}

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;

        if ($order->status !== OrderStatus::Delivered) {
            return;
        }

        $customer = $order->customer;

        if (! $customer) {
            return;
        }

        $points = $this->loyaltyService->calculatePointsFromOrderTotal((float) $order->total);

        if ($points <= 0) {
            return;
        }

        $this->loyaltyService->awardPoints(
            customer: $customer,
            points: $points,
            source: LoyaltyPointSource::OrderCompleted,
            description: __('Points earned from order :number', ['number' => $order->order_number]),
            reference: $order,
        );
    }
}
