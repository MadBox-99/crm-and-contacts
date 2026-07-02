<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceRuleType;
use App\Models\PriceRule;
use Illuminate\Support\Collection;

final class PricingService
{
    /**
     * Calculate the best price for a product given the context.
     *
     * @param  array{
     *     quantity?: float,
     *     total_value?: float,
     *     customer_id?: int,
     *     product_id?: int,
     *     team_id: int,
     * }  $context
     * @return array{
     *     original_price: float,
     *     final_price: float,
     *     total_discount: float,
     *     applied_rules: array<int, array{rule_id: int, name: string, discount: float}>,
     * }
     */
    public function calculatePrice(float $unitPrice, array $context): array
    {
        $rules = $this->getApplicableRules($context);

        if ($rules->isEmpty()) {
            $quantity = $context['quantity'] ?? 1;

            return [
                'original_price' => $unitPrice * $quantity,
                'final_price' => $unitPrice * $quantity,
                'total_discount' => 0.0,
                'applied_rules' => [],
            ];
        }

        return $this->applyRules($unitPrice, $context, $rules);
    }

    /**
     * Get all applicable rules for the given context, sorted by priority.
     *
     * @param  array<string, mixed>  $context
     * @return Collection<int, PriceRule>
     */
    public function getApplicableRules(array $context): Collection
    {
        $query = PriceRule::query()
            ->where('team_id', $context['team_id'])
            ->where('is_active', true)
            ->orderByDesc('priority');

        return $query->get()->filter(fn (PriceRule $rule): bool => $rule->isApplicable($context));
    }

    /**
     * Seed default quantity discount tiers for a team.
     *
     * @return array<int, PriceRule>
     */
    public function createDefaultQuantityTiers(int $teamId): array
    {
        $tiers = [
            ['min_qty' => 10, 'discount' => 5, 'priority' => 10, 'name' => '10+ items: 5%'],
            ['min_qty' => 25, 'discount' => 7, 'priority' => 20, 'name' => '25+ items: 7%'],
            ['min_qty' => 50, 'discount' => 10, 'priority' => 30, 'name' => '50+ items: 10%'],
            ['min_qty' => 100, 'discount' => 15, 'priority' => 40, 'name' => '100+ items: 15%'],
        ];

        $rules = [];

        foreach ($tiers as $tier) {
            $rules[] = PriceRule::query()->create([
                'team_id' => $teamId,
                'name' => $tier['name'],
                'rule_type' => PriceRuleType::Quantity,
                'conditions' => ['min_qty' => $tier['min_qty']],
                'discount_type' => 'percentage',
                'discount_value' => $tier['discount'],
                'priority' => $tier['priority'],
                'combinable' => false,
                'is_active' => true,
            ]);
        }

        return $rules;
    }

    /**
     * Apply the best discount or combine discounts based on rules.
     *
     * @param  Collection<int, PriceRule>  $rules
     * @return array{
     *     original_price: float,
     *     final_price: float,
     *     total_discount: float,
     *     applied_rules: array<int, array{rule_id: int, name: string, discount: float}>,
     * }
     */
    private function applyRules(float $unitPrice, array $context, Collection $rules): array
    {
        $quantity = $context['quantity'] ?? 1;
        $originalPrice = $unitPrice * $quantity;

        $combinableRules = $rules->filter(fn (PriceRule $rule): bool => $rule->combinable);
        $nonCombinableRules = $rules->reject(fn (PriceRule $rule): bool => (bool) $rule->combinable);

        // Calculate the best single non-combinable discount
        $bestSingleDiscount = 0.0;
        $bestSingleRule = null;

        foreach ($nonCombinableRules as $rule) {
            $discount = $rule->calculateDiscount($unitPrice, $quantity);
            if ($discount > $bestSingleDiscount) {
                $bestSingleDiscount = $discount;
                $bestSingleRule = $rule;
            }
        }

        // Calculate total combinable discount
        $combinableDiscount = 0.0;
        $appliedCombinableRules = [];

        foreach ($combinableRules as $rule) {
            $discount = $rule->calculateDiscount($unitPrice, $quantity);
            if ($discount > 0) {
                $combinableDiscount += $discount;
                $appliedCombinableRules[] = [
                    'rule_id' => $rule->id,
                    'name' => $rule->name,
                    'discount' => round($discount, 2),
                ];
            }
        }

        // Choose the better option: best single OR all combinable
        $appliedRules = [];
        $totalDiscount = 0.0;

        if ($bestSingleDiscount >= $combinableDiscount && $bestSingleRule) {
            // Use the best single non-combinable rule
            $totalDiscount = $bestSingleDiscount;
            $appliedRules[] = [
                'rule_id' => $bestSingleRule->id,
                'name' => $bestSingleRule->name,
                'discount' => round($bestSingleDiscount, 2),
            ];
        } else {
            // Use all combinable rules
            $totalDiscount = $combinableDiscount;
            $appliedRules = $appliedCombinableRules;
        }

        // If we used non-combinable, also check if adding combinable makes it better
        if ($bestSingleRule && $combinableDiscount > 0) {
            $combinedTotal = $bestSingleDiscount + $combinableDiscount;
            if ($combinedTotal > $totalDiscount) {
                // Check if the best single rule itself is NOT blocking combination
                // Only combine if combinable rules add value on top
                $totalDiscount = $bestSingleDiscount + $combinableDiscount;
                $appliedRules = [
                    [
                        'rule_id' => $bestSingleRule->id,
                        'name' => $bestSingleRule->name,
                        'discount' => round($bestSingleDiscount, 2),
                    ],
                    ...$appliedCombinableRules,
                ];
            }
        }

        // Cap discount at original price
        $totalDiscount = min($totalDiscount, $originalPrice);

        return [
            'original_price' => round($originalPrice, 2),
            'final_price' => round($originalPrice - $totalDiscount, 2),
            'total_discount' => round($totalDiscount, 2),
            'applied_rules' => $appliedRules,
        ];
    }
}
