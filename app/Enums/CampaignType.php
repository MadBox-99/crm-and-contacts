<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum CampaignType: string implements HasColor, HasIcon, HasLabel
{
    case GoogleAds = 'google_ads';
    case Email = 'email';
    case Phone = 'phone';
    case Event = 'event';
    case Other = 'other';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::GoogleAds => 'Google Ads',
            self::Email => 'Email',
            self::Phone => 'Phone',
            self::Event => 'Event',
            self::Other => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GoogleAds => 'success',
            self::Email => 'info',
            self::Phone => 'warning',
            self::Event => 'purple',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::GoogleAds => 'heroicon-o-chart-bar',
            self::Email => 'heroicon-o-envelope',
            self::Phone => 'heroicon-o-phone',
            self::Event => 'heroicon-o-calendar',
            self::Other => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    public function isGoogleAds(): bool
    {
        return $this === self::GoogleAds;
    }

    public function requiresExternalSync(): bool
    {
        return in_array($this, [self::GoogleAds, self::Email], true);
    }
}
