<?php

declare(strict_types=1);

use App\Enums\CampaignResponseType;
use App\Models\Campaign;
use App\Models\CampaignResponse;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be created with factory', function (): void {
    $response = CampaignResponse::factory()->create();

    expect($response)->toBeInstanceOf(CampaignResponse::class);
});

it('has fillable attributes', function (): void {
    $campaign = Campaign::factory()->create();
    $customer = Customer::factory()->create();

    $response = CampaignResponse::factory()->create([
        'campaign_id' => $campaign->id,
        'customer_id' => $customer->id,
        'response_type' => CampaignResponseType::Interested,
        'notes' => 'Customer interested',
        'responded_at' => now(),
    ]);

    expect($response->campaign_id)->toBe($campaign->id)
        ->and($response->customer_id)->toBe($customer->id)
        ->and($response->response_type)->toBe(CampaignResponseType::Interested)
        ->and($response->notes)->toBe('Customer interested');
});

it('casts responded_at to datetime', function (): void {
    $response = CampaignResponse::factory()->create(['responded_at' => now()]);

    expect($response->responded_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('has campaign relationship', function (): void {
    $campaign = Campaign::factory()->create();
    $response = CampaignResponse::factory()->create(['campaign_id' => $campaign->id]);

    expect($response->campaign)->toBeInstanceOf(Campaign::class)
        ->and($response->campaign->id)->toBe($campaign->id);
});

it('has customer relationship', function (): void {
    $customer = Customer::factory()->create();
    $response = CampaignResponse::factory()->create(['customer_id' => $customer->id]);

    expect($response->customer)->toBeInstanceOf(Customer::class)
        ->and($response->customer->id)->toBe($customer->id);
});
