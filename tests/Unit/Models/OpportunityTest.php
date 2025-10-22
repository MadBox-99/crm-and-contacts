<?php

declare(strict_types=1);

use App\Enums\OpportunityStage;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $opportunity = Opportunity::factory()->create();

    expect($opportunity)->toBeInstanceOf(Opportunity::class)
        ->and($opportunity->title)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $customer = Customer::factory()->create();
    $user = User::factory()->create();

    $opportunity = Opportunity::factory()->create([
        'customer_id' => $customer->id,
        'title' => 'Test Opportunity',
        'description' => 'Test Description',
        'value' => 100000.00,
        'probability' => 75,
        'stage' => OpportunityStage::Qualified,
        'expected_close_date' => now()->addDays(30),
        'assigned_to' => $user->id,
    ]);

    expect($opportunity->customer_id)->toBe($customer->id)
        ->and($opportunity->title)->toBe('Test Opportunity')
        ->and($opportunity->description)->toBe('Test Description')
        ->and($opportunity->value)->toBe('100000.00')
        ->and($opportunity->probability)->toBe(75)
        ->and($opportunity->stage)->toBe(OpportunityStage::Qualified)
        ->and($opportunity->assigned_to)->toBe($user->id);
});

it('casts value to decimal', function (): void {
    $opportunity = Opportunity::factory()->create(['value' => 100000.00]);

    expect($opportunity->value)->toBe('100000.00');
});

it('casts probability to integer', function (): void {
    $opportunity = Opportunity::factory()->create(['probability' => 75]);

    expect($opportunity->probability)->toBe(75)
        ->and($opportunity->probability)->toBeInt();
});

it('casts expected_close_date to date', function (): void {
    $opportunity = Opportunity::factory()->create(['expected_close_date' => '2025-02-15']);

    expect($opportunity->expected_close_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts stage to OpportunityStage enum', function (): void {
    $opportunity = Opportunity::factory()->create(['stage' => OpportunityStage::Qualified]);

    expect($opportunity->stage)->toBeInstanceOf(OpportunityStage::class)
        ->and($opportunity->stage)->toBe(OpportunityStage::Qualified);
});

it('uses soft deletes', function (): void {
    $opportunity = Opportunity::factory()->create();
    $opportunity->delete();

    expect($opportunity->trashed())->toBeTrue();
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $opportunity = Opportunity::factory()->create(['customer_id' => $customer->id]);

    expect($opportunity->customer)->toBeInstanceOf(Customer::class)
        ->and($opportunity->customer->id)->toBe($customer->id);
});

it('has assignedUser relationship', function (): void {
    $user = User::factory()->create();
    $opportunity = Opportunity::factory()->create(['assigned_to' => $user->id]);

    expect($opportunity->assignedUser)->toBeInstanceOf(User::class)
        ->and($opportunity->assignedUser->id)->toBe($user->id);
});
