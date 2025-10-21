<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\CampaignResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $campaign = Campaign::factory()->create();

    expect($campaign)->toBeInstanceOf(Campaign::class)
        ->and($campaign->name)->not->toBeEmpty();
});

it('has fillable attributes', function (): void {
    $user = User::factory()->create();

    $campaign = Campaign::factory()->create([
        'name' => 'Test Campaign',
        'description' => 'Test Description',
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'status' => 'active',
        'target_audience_criteria' => ['age' => '25-45'],
        'created_by' => $user->id,
    ]);

    expect($campaign->name)->toBe('Test Campaign')
        ->and($campaign->description)->toBe('Test Description')
        ->and($campaign->status)->toBe('active')
        ->and($campaign->target_audience_criteria)->toBe(['age' => '25-45'])
        ->and($campaign->created_by)->toBe($user->id);
});

it('casts date fields to date', function (): void {
    $campaign = Campaign::factory()->create([
        'start_date' => '2025-01-15',
        'end_date' => '2025-02-15',
    ]);

    expect($campaign->start_date)->toBeInstanceOf(DateTimeInterface::class)
        ->and($campaign->end_date)->toBeInstanceOf(DateTimeInterface::class);
});

it('casts target_audience_criteria to array', function (): void {
    $campaign = Campaign::factory()->create([
        'target_audience_criteria' => ['age' => '25-45', 'location' => 'Budapest'],
    ]);

    expect($campaign->target_audience_criteria)->toBeArray()
        ->and($campaign->target_audience_criteria)->toBe(['age' => '25-45', 'location' => 'Budapest']);
});

it('uses soft deletes', function (): void {
    $campaign = Campaign::factory()->create();
    $campaign->delete();

    expect($campaign->trashed())->toBeTrue();
});

it('has creator relationship', function (): void {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create(['created_by' => $user->id]);

    expect($campaign->creator)->toBeInstanceOf(User::class)
        ->and($campaign->creator->id)->toBe($user->id);
});

it('has responses relationship', function (): void {
    $campaign = Campaign::factory()->create();
    $response = CampaignResponse::factory()->create(['campaign_id' => $campaign->id]);

    expect($campaign->responses)->toHaveCount(1)
        ->and($campaign->responses->first()->id)->toBe($response->id);
});
