<?php

declare(strict_types=1);

namespace App\Enums;

enum DiscountValueType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';
}
