<?php

declare(strict_types=1);

namespace App\Enums;

enum CustomerType: string
{
    case Individual = 'individual';
    case Company = 'company';
}
