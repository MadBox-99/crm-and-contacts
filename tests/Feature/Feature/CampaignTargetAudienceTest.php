<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('can attach customers to campaign target audience', function (): void {
    $campaign = Campaign::factory()->create();
    $customers = Customer::factory()->count(3)->create();

    $campaign->targetAudience()->attach($customers->pluck('id'));

    expect($campaign->targetAudience)->toHaveCount(3);
    expect($campaign->targetAudience->pluck('id')->toArray())
        ->toMatchArray($customers->pluck('id')->toArray());
});

it('can detach customers from campaign target audience', function (): void {
    $campaign = Campaign::factory()->create();
    $customers = Customer::factory()->count(3)->create();
    $campaign->targetAudience()->attach($customers->pluck('id'));

    $campaign->targetAudience()->detach($customers->first()->id);

    expect($campaign->targetAudience)->toHaveCount(2);
    expect($campaign->targetAudience->contains($customers->first()))->toBeFalse();
});

it('stores pivot data when attaching customers', function (): void {
    $user = User::factory()->create();
    Auth::login($user);

    $campaign = Campaign::factory()->create();
    $customer = Customer::factory()->create();

    $campaign->targetAudience()->attach($customer->id, [
        'notes' => 'High value customer',
        'added_at' => now(),
        'added_by' => $user->id,
    ]);

    assertDatabaseHas('campaign_customer', [
        'campaign_id' => $campaign->id,
        'customer_id' => $customer->id,
        'notes' => 'High value customer',
        'added_by' => $user->id,
    ]);
});

it('can retrieve target audience with pivot data', function (): void {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create();
    $customer = Customer::factory()->create();

    $campaign->targetAudience()->attach($customer->id, [
        'notes' => 'VIP customer',
        'added_at' => now(),
        'added_by' => $user->id,
    ]);

    $audience = $campaign->targetAudience()->first();

    expect($audience->pivot->notes)->toBe('VIP customer');
    expect($audience->pivot->added_by)->toBe($user->id);
});

it('prevents duplicate customers in target audience', function (): void {
    $campaign = Campaign::factory()->create();
    $customer = Customer::factory()->create();

    $campaign->targetAudience()->attach($customer->id);

    expect(fn () => $campaign->targetAudience()->attach($customer->id))
        ->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});

it('can sync target audience', function (): void {
    $campaign = Campaign::factory()->create();
    $oldCustomers = Customer::factory()->count(3)->create();
    $newCustomers = Customer::factory()->count(2)->create();

    $campaign->targetAudience()->attach($oldCustomers->pluck('id'));
    $campaign->targetAudience()->sync($newCustomers->pluck('id'));

    expect($campaign->targetAudience)->toHaveCount(2);
    expect($campaign->targetAudience->pluck('id')->toArray())
        ->toMatchArray($newCustomers->pluck('id')->toArray());
});

it('removes target audience when campaign is force deleted', function (): void {
    $campaign = Campaign::factory()->create();
    $customers = Customer::factory()->count(3)->create();
    $campaign->targetAudience()->attach($customers->pluck('id'));

    $campaign->forceDelete();

    assertDatabaseCount('campaign_customer', 0);
});

it('can count target audience members', function (): void {
    $campaign = Campaign::factory()->create();
    $customers = Customer::factory()->count(5)->create();
    $campaign->targetAudience()->attach($customers->pluck('id'));

    expect($campaign->targetAudience()->count())->toBe(5);
});

it('can filter target audience by customer type', function (): void {
    $campaign = Campaign::factory()->create();
    $individualCustomers = Customer::factory()->count(3)->create(['type' => \App\Enums\CustomerType::Individual]);
    $companyCustomers = Customer::factory()->count(2)->create(['type' => \App\Enums\CustomerType::Company]);

    $campaign->targetAudience()->attach(
        $individualCustomers->merge($companyCustomers)->pluck('id')
    );

    $filteredAudience = $campaign->targetAudience()
        ->where('type', \App\Enums\CustomerType::Individual)
        ->get();

    expect($filteredAudience)->toHaveCount(3);
});
