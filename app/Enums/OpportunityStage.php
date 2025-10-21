<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum OpportunityStage: string implements HasColor, HasLabel
{
    case Lead = 'lead';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Negotiation = 'negotiation';
    case SendedQuotation = 'sended_quotation';
    case LostQuotation = 'lost_quotation';

    public static function getActiveStages(): array
    {
        return [
            self::Lead,
            self::Qualified,
            self::Proposal,
            self::Negotiation,
        ];
    }

    public static function getClosedStages(): array
    {
        return [
            self::SendedQuotation,
            self::LostQuotation,
        ];
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Lead => __('Lead'),
            self::Qualified => __('Qualified'),
            self::Proposal => __('Proposal'),
            self::Negotiation => __('Negotiation'),
            self::SendedQuotation => __('Sended Quotation'),
            self::LostQuotation => __('Lost Quotation'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Lead => 'gray',
            self::Qualified => 'info',
            self::Proposal => 'warning',
            self::Negotiation => 'primary',
            self::SendedQuotation => 'success',
            self::LostQuotation => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, self::getActiveStages(), true);
    }

    public function isClosed(): bool
    {
        return in_array($this, self::getClosedStages(), true);
    }
}
