<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum NavigationGroup: string implements HasIcon, HasLabel
{
    case Customers = 'Customers';
    case Support = 'Support';
    case Sales = 'Sales';
    case Products = 'Products';
    case Marketing = 'Marketing';
    case Communication = 'Communication';
    case Reports = 'Reports';
    case Settings = 'Settings';
    case System = 'System';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Customers => __('Customers'),
            self::Support => __('Support'),
            self::Sales => __('Sales'),
            self::Products => __('Products'),
            self::Marketing => __('Marketing'),
            self::Communication => __('Communication'),
            self::Reports => __('Reports'),
            self::Settings => __('Settings'),
            self::System => __('System'),
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::Customers => 'heroicon-o-user-group',
            self::Support => 'heroicon-o-lifebuoy',
            self::Sales => 'heroicon-o-currency-dollar',
            self::Products => 'heroicon-o-cube',
            self::Marketing => 'heroicon-o-megaphone',
            self::Communication => 'heroicon-o-chat-bubble-left-right',
            self::Reports => 'heroicon-o-chart-bar',
            self::Settings => 'heroicon-o-cog-6-tooth',
            self::System => 'heroicon-o-server',
        };
    }

    public function getSort(): int
    {
        return match ($this) {
            self::Customers => 10,
            self::Support => 20,
            self::Sales => 30,
            self::Products => 40,
            self::Marketing => 50,
            self::Communication => 60,
            self::Reports => 70,
            self::Settings => 80,
            self::System => 90,
        };
    }
}
