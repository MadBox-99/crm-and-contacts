<?php

declare(strict_types=1);

use App\Enums\DiscountValueType;
use App\Enums\PriceRuleType;
use App\Models\Customer;
use App\Models\PriceRule;
use App\Models\Product;
use App\Models\Team;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->pricingService = resolve(PricingService::class);
    $this->team = Team::factory()->create();
    $this->customer = Customer::factory()->for($this->team)->create();
    $this->product = Product::factory()->for($this->team)->create(['unit_price' => 1000]);
});

it('returns original price when no rules match', function (): void {
    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 5,
    ]);

    expect($result['original_price'])->toBe(5000.0)
        ->and($result['final_price'])->toBe(5000.0)
        ->and($result['total_discount'])->toBe(0.0)
        ->and($result['applied_rules'])->toBeEmpty();
});

it('applies quantity discount for 10+ items', function (): void {
    PriceRule::factory()->for($this->team)->quantity(10, 5)->create([
        'priority' => 10,
    ]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 15,
    ]);

    expect($result['original_price'])->toBe(15000.0)
        ->and($result['total_discount'])->toBe(750.0)
        ->and($result['final_price'])->toBe(14250.0)
        ->and($result['applied_rules'])->toHaveCount(1);
});

it('applies higher quantity tier over lower', function (): void {
    PriceRule::factory()->for($this->team)->quantity(10, 5)->create(['priority' => 10]);
    PriceRule::factory()->for($this->team)->quantity(50, 10)->create(['priority' => 20]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 60,
    ]);

    // Both rules match, but 10% gives more discount than 5%, so best single wins
    expect($result['total_discount'])->toBe(6000.0) // 60 * 1000 * 10%
        ->and($result['final_price'])->toBe(54000.0);
});

it('applies value threshold discount', function (): void {
    PriceRule::factory()->for($this->team)->valueThreshold(100000, 3)->create([
        'priority' => 10,
    ]);

    $result = $this->pricingService->calculatePrice(5000, [
        'team_id' => $this->team->id,
        'quantity' => 25,
        'total_value' => 125000,
    ]);

    expect($result['original_price'])->toBe(125000.0)
        ->and($result['total_discount'])->toBe(3750.0) // 125000 * 3%
        ->and($result['final_price'])->toBe(121250.0);
});

it('applies customer-specific discount', function (): void {
    PriceRule::factory()->for($this->team)->customerSpecific(
        $this->customer->id,
        $this->product->id,
    )->create([
        'discount_type' => DiscountValueType::Fixed,
        'discount_value' => 200,
        'priority' => 50,
    ]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 3,
        'customer_id' => $this->customer->id,
        'product_id' => $this->product->id,
    ]);

    expect($result['total_discount'])->toBe(600.0) // 200 * 3
        ->and($result['final_price'])->toBe(2400.0);
});

it('applies seasonal discount within valid dates', function (): void {
    PriceRule::factory()->for($this->team)->seasonal(
        now()->subDay()->toDateString(),
        now()->addMonth()->toDateString(),
    )->create([
        'discount_type' => DiscountValueType::Percentage,
        'discount_value' => 15,
        'priority' => 5,
    ]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 10,
    ]);

    expect($result['total_discount'])->toBe(1500.0)
        ->and($result['applied_rules'])->toHaveCount(1);
});

it('does not apply expired seasonal discount', function (): void {
    PriceRule::factory()->for($this->team)->seasonal(
        now()->subMonth()->toDateString(),
        now()->subDay()->toDateString(),
    )->create([
        'discount_type' => DiscountValueType::Percentage,
        'discount_value' => 15,
        'priority' => 5,
    ]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 10,
    ]);

    expect($result['total_discount'])->toBe(0.0)
        ->and($result['applied_rules'])->toBeEmpty();
});

it('combines combinable rules for better discount', function (): void {
    // Non-combinable: 5% quantity
    PriceRule::factory()->for($this->team)->quantity(10, 5)->create([
        'priority' => 10,
        'combinable' => false,
    ]);

    // Combinable: 2% seasonal
    PriceRule::factory()->for($this->team)->seasonal(
        now()->subDay()->toDateString(),
        now()->addMonth()->toDateString(),
    )->create([
        'discount_type' => DiscountValueType::Percentage,
        'discount_value' => 2,
        'priority' => 5,
        'combinable' => true,
    ]);

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 20,
    ]);

    // Best single (5%) = 1000, combinable (2%) = 400, combined = 1400
    expect($result['total_discount'])->toBe(1400.0)
        ->and($result['applied_rules'])->toHaveCount(2);
});

it('ignores inactive rules', function (): void {
    PriceRule::factory()->for($this->team)->quantity(10, 50)->inactive()->create();

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 20,
    ]);

    expect($result['total_discount'])->toBe(0.0);
});

it('does not apply rules from another team', function (): void {
    $otherTeam = Team::factory()->create();
    PriceRule::factory()->for($otherTeam)->quantity(10, 50)->create();

    $result = $this->pricingService->calculatePrice(1000, [
        'team_id' => $this->team->id,
        'quantity' => 20,
    ]);

    expect($result['total_discount'])->toBe(0.0);
});

it('creates default quantity tiers', function (): void {
    $rules = $this->pricingService->createDefaultQuantityTiers($this->team->id);

    expect($rules)->toHaveCount(4);

    $ruleNames = collect($rules)->pluck('name')->all();
    expect($ruleNames)->toContain('10+ items: 5%')
        ->toContain('50+ items: 10%');
});

it('caps discount at original price', function (): void {
    PriceRule::factory()->for($this->team)->create([
        'rule_type' => PriceRuleType::Quantity,
        'conditions' => ['min_qty' => 1],
        'discount_type' => DiscountValueType::Fixed,
        'discount_value' => 99999,
        'priority' => 10,
    ]);

    $result = $this->pricingService->calculatePrice(100, [
        'team_id' => $this->team->id,
        'quantity' => 2,
    ]);

    expect($result['final_price'])->toBe(0.0)
        ->and($result['total_discount'])->toBe(200.0);
});
