<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum DiscountType: string implements HasLabel
{
    case Quantity = 'quantity';
    case ValueThreshold = 'value_threshold';
    case TimeBased = 'time_based';
    case Custom = 'custom';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Quantity => __('Quantity'),
            self::ValueThreshold => __('Value Threshold'),
            self::TimeBased => __('Time-Based'),
            self::Custom => __('Custom'),
        };
    }
}
