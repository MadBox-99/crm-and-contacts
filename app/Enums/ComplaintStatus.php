<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ComplaintStatus: string implements HasLabel
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Open => __('Open'),
            self::InProgress => __('In Progress'),
            self::Resolved => __('Resolved'),
            self::Closed => __('Closed'),
        };
    }
}
