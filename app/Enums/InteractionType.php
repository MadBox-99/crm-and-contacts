<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum InteractionType: string implements HasLabel
{
    case Call = 'call';
    case Email = 'email';
    case Meeting = 'meeting';
    case Note = 'note';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Call => __('Call'),
            self::Email => __('Email'),
            self::Meeting => __('Meeting'),
            self::Note => __('Note'),
        };
    }
}
