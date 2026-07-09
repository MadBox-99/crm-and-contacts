<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Interaction;
use App\Models\LeadScore;
use App\Models\Team;
use App\Models\User;
use App\Services\CustomerDeduplicator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop the unique index so the test can seed the duplicate rows that
 * previously existed in production before the constraint was introduced.
 */
function allowDuplicateCustomers(): void
{
    Schema::table('customers', function (Blueprint $table): void {
        $table->dropUnique('customers_team_id_email_unique');
    });
}

it('merges duplicate customers, keeping the oldest and reassigning children', function (): void {
    allowDuplicateCustomers();

    $team = Team::factory()->create();
    $user = User::factory()->create();

    $survivor = Customer::factory()->for($team)->create(['email' => 'dup@example.com']);
    $duplicate = Customer::factory()->for($team)->create(['email' => 'dup@example.com']);

    $interaction = Interaction::factory()
        ->for($team)
        ->for($duplicate)
        ->for($user)
        ->create();

    $merged = app(CustomerDeduplicator::class)->deduplicate();

    expect($merged)->toBe(1)
        ->and(Customer::query()->where('email', 'dup@example.com')->count())->toBe(1)
        ->and($survivor->fresh())->not->toBeNull()
        ->and(Customer::withTrashed()->find($duplicate->id))->toBeNull()
        ->and($interaction->fresh()->customer_id)->toBe($survivor->id);
});

it('resolves child unique-constraint collisions when merging', function (): void {
    allowDuplicateCustomers();

    $team = Team::factory()->create();

    $survivor = Customer::factory()->for($team)->create(['email' => 'dup@example.com']);
    $duplicate = Customer::factory()->for($team)->create(['email' => 'dup@example.com']);

    // Both customers have a lead score in the same team => unique(team_id, customer_id) collision on merge.
    LeadScore::factory()->for($team)->for($survivor)->create();
    LeadScore::factory()->for($team)->for($duplicate)->create();

    $merged = app(CustomerDeduplicator::class)->deduplicate();

    expect($merged)->toBe(1)
        ->and(LeadScore::query()->where('customer_id', $survivor->id)->count())->toBe(1)
        ->and(LeadScore::query()->where('customer_id', $duplicate->id)->count())->toBe(0);
});

it('does not touch customers with distinct emails or null emails', function (): void {
    allowDuplicateCustomers();

    $team = Team::factory()->create();

    Customer::factory()->for($team)->create(['email' => 'a@example.com']);
    Customer::factory()->for($team)->create(['email' => 'b@example.com']);
    Customer::factory()->for($team)->create(['email' => null]);
    Customer::factory()->for($team)->create(['email' => null]);

    $merged = app(CustomerDeduplicator::class)->deduplicate();

    expect($merged)->toBe(0)
        ->and(Customer::query()->count())->toBe(4);
});
