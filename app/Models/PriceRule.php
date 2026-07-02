<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DiscountValueType;
use App\Enums\PriceRuleType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\PriceRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Override;

final class PriceRule extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<PriceRuleFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'rule_type',
        'conditions',
        'discount_type',
        'discount_value',
        'priority',
        'combinable',
        'is_active',
        'customer_id',
        'product_id',
        'description',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if this rule is applicable for the given context.
     *
     * @param  array{quantity?: float, total_value?: float, customer_id?: int, product_id?: int}  $context
     */
    public function isApplicable(array $context): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($this->rule_type) {
            PriceRuleType::Quantity => $this->isQuantityApplicable($context),
            PriceRuleType::ValueThreshold => $this->isValueThresholdApplicable($context),
            PriceRuleType::CustomerSpecific => $this->isCustomerSpecificApplicable($context),
            PriceRuleType::Seasonal => $this->isSeasonalApplicable(),
            default => false,
        };
    }

    /**
     * Calculate the discount amount for a given unit price and quantity.
     */
    public function calculateDiscount(float $unitPrice, float $quantity): float
    {
        $baseAmount = $unitPrice * $quantity;

        return match ($this->discount_type) {
            DiscountValueType::Percentage => $baseAmount * ((float) $this->discount_value / 100),
            DiscountValueType::Fixed => min((float) $this->discount_value * $quantity, $baseAmount),
            default => 0.0,
        };
    }

    /** @return array<string, mixed> */
    public function getConditions(): array
    {
        return $this->conditions ?? [];
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'discount_value' => 'decimal:2',
            'priority' => 'integer',
            'combinable' => 'boolean',
            'is_active' => 'boolean',
            'rule_type' => PriceRuleType::class,
            'discount_type' => DiscountValueType::class,
        ];
    }

    private function isQuantityApplicable(array $context): bool
    {
        $minQty = $this->getConditions()['min_qty'] ?? 0;

        return isset($context['quantity']) && $context['quantity'] >= $minQty;
    }

    private function isValueThresholdApplicable(array $context): bool
    {
        $minValue = $this->getConditions()['min_value'] ?? 0;

        return isset($context['total_value']) && $context['total_value'] >= $minValue;
    }

    private function isCustomerSpecificApplicable(array $context): bool
    {
        if (! $this->customer_id) {
            return false;
        }

        $customerMatch = isset($context['customer_id']) && $context['customer_id'] === $this->customer_id;
        $productMatch = ! $this->product_id || (isset($context['product_id']) && $context['product_id'] === $this->product_id);

        return $customerMatch && $productMatch;
    }

    private function isSeasonalApplicable(): bool
    {
        $conditions = $this->getConditions();
        $now = now();

        $validFrom = isset($conditions['valid_from']) ? Date::parse($conditions['valid_from']) : null;
        $validTo = isset($conditions['valid_to']) ? Date::parse($conditions['valid_to']) : null;

        if ($validFrom && $now->lt($validFrom)) {
            return false;
        }

        return ! ($validTo && $now->gt($validTo));
    }
}
