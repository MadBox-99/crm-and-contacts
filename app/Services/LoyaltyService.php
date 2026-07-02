<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoyaltyPointSource;
use App\Enums\LoyaltyTransactionType;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use Illuminate\Database\Eloquent\Model;

final class LoyaltyService
{
    /**
     * Award points to a customer.
     */
    public function awardPoints(
        Customer $customer,
        int $points,
        LoyaltyPointSource $source,
        ?string $description = null,
        ?Model $reference = null,
    ): LoyaltyPoint {
        $newBalance = $customer->loyalty_points + $points;

        $transaction = LoyaltyPoint::query()->create([
            'team_id' => $customer->team_id,
            'customer_id' => $customer->id,
            'points' => $points,
            'type' => LoyaltyTransactionType::Earned,
            'source' => $source,
            'description' => $description,
            'reference_type' => $reference instanceof Model ? $reference->getMorphClass() : null,
            'reference_id' => $reference?->getKey(),
            'balance_after' => $newBalance,
        ]);

        $customer->update(['loyalty_points' => $newBalance]);
        $customer->recalculateLoyaltyLevel();

        return $transaction;
    }

    /**
     * Deduct points from a customer.
     */
    public function deductPoints(
        Customer $customer,
        int $points,
        LoyaltyPointSource $source,
        ?string $description = null,
        ?Model $reference = null,
    ): LoyaltyPoint {
        $newBalance = max(0, $customer->loyalty_points - $points);

        $transaction = LoyaltyPoint::query()->create([
            'team_id' => $customer->team_id,
            'customer_id' => $customer->id,
            'points' => -$points,
            'type' => LoyaltyTransactionType::Spent,
            'source' => $source,
            'description' => $description,
            'reference_type' => $reference instanceof Model ? $reference->getMorphClass() : null,
            'reference_id' => $reference?->getKey(),
            'balance_after' => $newBalance,
        ]);

        $customer->update(['loyalty_points' => $newBalance]);
        $customer->recalculateLoyaltyLevel();

        return $transaction;
    }

    /**
     * Manually adjust points (can be positive or negative).
     */
    public function adjustPoints(
        Customer $customer,
        int $points,
        ?string $description = null,
    ): LoyaltyPoint {
        $newBalance = max(0, $customer->loyalty_points + $points);

        $type = $points >= 0
            ? LoyaltyTransactionType::Earned
            : LoyaltyTransactionType::Adjusted;

        $transaction = LoyaltyPoint::query()->create([
            'team_id' => $customer->team_id,
            'customer_id' => $customer->id,
            'points' => $points,
            'type' => $type,
            'source' => LoyaltyPointSource::ManualAdjustment,
            'description' => $description,
            'balance_after' => $newBalance,
        ]);

        $customer->update(['loyalty_points' => $newBalance]);
        $customer->recalculateLoyaltyLevel();

        return $transaction;
    }

    /**
     * Calculate points from an order total.
     * Default: 1 point per 1000 Ft.
     */
    public function calculatePointsFromOrderTotal(float $total, int $pointsPer1000 = 1): int
    {
        return (int) floor($total / 1000) * $pointsPer1000;
    }
}
