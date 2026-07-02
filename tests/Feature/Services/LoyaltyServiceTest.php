<?php

declare(strict_types=1);

use App\Enums\LoyaltyPointSource;
use App\Enums\LoyaltyTransactionType;
use App\Models\Customer;
use App\Models\LoyaltyLevel;
use App\Models\Order;
use App\Models\Team;
use App\Services\LoyaltyService;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->customer = Customer::factory()->create([
        'team_id' => $this->team->id,
        'loyalty_points' => 0,
    ]);
    $this->service = resolve(LoyaltyService::class);
});

it('awards points to a customer', function (): void {
    $transaction = $this->service->awardPoints(
        customer: $this->customer,
        points: 100,
        source: LoyaltyPointSource::ManualAdjustment,
        description: 'Test award',
    );

    $this->customer->refresh();

    expect($transaction->points)->toBe(100)
        ->and($transaction->type)->toBe(LoyaltyTransactionType::Earned)
        ->and($transaction->balance_after)->toBe(100)
        ->and($this->customer->loyalty_points)->toBe(100);
});

it('deducts points from a customer', function (): void {
    $this->customer->update(['loyalty_points' => 500]);

    $transaction = $this->service->deductPoints(
        customer: $this->customer,
        points: 200,
        source: LoyaltyPointSource::ManualAdjustment,
        description: 'Test deduction',
    );

    $this->customer->refresh();

    expect($transaction->points)->toBe(-200)
        ->and($transaction->type)->toBe(LoyaltyTransactionType::Spent)
        ->and($transaction->balance_after)->toBe(300)
        ->and($this->customer->loyalty_points)->toBe(300);
});

it('does not allow negative balance when deducting', function (): void {
    $this->customer->update(['loyalty_points' => 50]);

    $this->service->deductPoints(
        customer: $this->customer,
        points: 200,
        source: LoyaltyPointSource::ManualAdjustment,
    );

    $this->customer->refresh();

    expect($this->customer->loyalty_points)->toBe(0);
});

it('adjusts points positively', function (): void {
    $transaction = $this->service->adjustPoints(
        customer: $this->customer,
        points: 500,
        description: 'Bonus adjustment',
    );

    $this->customer->refresh();

    expect($transaction->points)->toBe(500)
        ->and($transaction->source)->toBe(LoyaltyPointSource::ManualAdjustment)
        ->and($this->customer->loyalty_points)->toBe(500);
});

it('adjusts points negatively', function (): void {
    $this->customer->update(['loyalty_points' => 300]);

    $transaction = $this->service->adjustPoints(
        customer: $this->customer,
        points: -100,
        description: 'Correction',
    );

    $this->customer->refresh();

    expect($transaction->points)->toBe(-100)
        ->and($transaction->type)->toBe(LoyaltyTransactionType::Adjusted)
        ->and($this->customer->loyalty_points)->toBe(200);
});

it('auto-calculates loyalty level after awarding points', function (): void {
    LoyaltyLevel::factory()->bronze()->create(['team_id' => $this->team->id]);
    $silverLevel = LoyaltyLevel::factory()->silver()->create(['team_id' => $this->team->id]);
    LoyaltyLevel::factory()->gold()->create(['team_id' => $this->team->id]);

    $this->service->awardPoints(
        customer: $this->customer,
        points: 1500,
        source: LoyaltyPointSource::ManualAdjustment,
    );

    $this->customer->refresh();

    expect($this->customer->loyalty_level_id)->toBe($silverLevel->id);
});

it('upgrades loyalty level when threshold is crossed', function (): void {
    LoyaltyLevel::factory()->bronze()->create(['team_id' => $this->team->id]);
    LoyaltyLevel::factory()->silver()->create(['team_id' => $this->team->id]);
    $goldLevel = LoyaltyLevel::factory()->gold()->create(['team_id' => $this->team->id]);

    $this->customer->update(['loyalty_points' => 4000]);

    $this->service->awardPoints(
        customer: $this->customer,
        points: 1500,
        source: LoyaltyPointSource::OrderCompleted,
    );

    $this->customer->refresh();

    expect($this->customer->loyalty_level_id)->toBe($goldLevel->id)
        ->and($this->customer->loyalty_points)->toBe(5500);
});

it('calculates points from order total', function (): void {
    expect($this->service->calculatePointsFromOrderTotal(5000.00))->toBe(5)
        ->and($this->service->calculatePointsFromOrderTotal(999.99))->toBe(0)
        ->and($this->service->calculatePointsFromOrderTotal(10500.50))->toBe(10)
        ->and($this->service->calculatePointsFromOrderTotal(0))->toBe(0);
});

it('awards points with a reference model', function (): void {
    $order = Order::factory()->create([
        'team_id' => $this->team->id,
        'customer_id' => $this->customer->id,
    ]);

    $transaction = $this->service->awardPoints(
        customer: $this->customer,
        points: 50,
        source: LoyaltyPointSource::OrderCompleted,
        description: 'Order reward',
        reference: $order,
    );

    expect($transaction->reference_type)->toBe('order')
        ->and($transaction->reference_id)->toBe($order->id);
});
