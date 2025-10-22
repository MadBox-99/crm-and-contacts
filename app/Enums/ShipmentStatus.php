<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ShipmentStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Processing => __('Processing'),
            self::Shipped => __('Shipped'),
            self::InTransit => __('In Transit'),
            self::OutForDelivery => __('Out for Delivery'),
            self::Delivered => __('Delivered'),
            self::Failed => __('Failed'),
            self::Returned => __('Returned'),
            self::Cancelled => __('Cancelled'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'info',
            self::Shipped => 'primary',
            self::InTransit => 'warning',
            self::OutForDelivery => 'warning',
            self::Delivered => 'success',
            self::Failed => 'danger',
            self::Returned => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
