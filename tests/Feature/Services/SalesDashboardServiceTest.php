<?php

declare(strict_types=1);

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintType;
use App\Models\Campaign;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\Team;
use App\Services\SalesDashboardService;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->service = resolve(SalesDashboardService::class);
});

it('returns KPIs with revenue and conversion data', function (): void {
    Order::factory()->for($this->team)->count(3)->create([
        'order_date' => now(),
        'total' => 10000,
    ]);

    Quote::factory()->for($this->team)->create([
        'status' => 'draft',
        'issue_date' => now(),
    ]);
    Quote::factory()->for($this->team)->create([
        'status' => 'accepted',
        'issue_date' => now(),
    ]);

    $kpis = $this->service->getKpis(1);

    expect($kpis)
        ->toHaveKeys(['monthly_revenue', 'active_quotes', 'conversion_rate', 'avg_deal_size'])
        ->and($kpis['monthly_revenue'])->toBe(30000.0)
        ->and($kpis['active_quotes'])->toBe(1)
        ->and($kpis['avg_deal_size'])->toBe(10000.0);
});

it('returns pipeline data from CrmReportingService', function (): void {
    $pipeline = $this->service->getPipelineData();

    expect($pipeline)->toHaveKeys(['stages', 'total_pipeline_value', 'weighted_value']);
});

it('returns trend data from CrmReportingService', function (): void {
    $trend = $this->service->getTrendData(6);

    expect($trend)->toHaveKey('trend')
        ->and($trend['trend'])->toHaveCount(6);
});

it('returns campaign ROI data', function (): void {
    $campaign = Campaign::factory()->for($this->team)->create([
        'budget' => 100000,
        'actual_cost' => 50000,
    ]);

    DB::table('campaign_conversions')->insert([
        'campaign_id' => $campaign->id,
        'conversion_date' => now(),
        'conversion_value' => 200000,
        'cost_at_conversion' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $roiData = $this->service->getCampaignRoiData();

    expect($roiData)->toHaveCount(1)
        ->and($roiData[0]['name'])->toBe($campaign->name)
        ->and($roiData[0]['budget'])->toBe(100000.0)
        ->and($roiData[0]['revenue'])->toBe(200000.0)
        ->and($roiData[0]['conversions'])->toBe(1);
});

it('returns complaint statistics by type and severity', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    Complaint::factory()->for($this->team)->for($customer)->create([
        'type' => ComplaintType::Product,
        'severity' => ComplaintSeverity::High,
        'status' => ComplaintStatus::Open,
    ]);

    Complaint::factory()->for($this->team)->for($customer)->create([
        'type' => ComplaintType::Billing,
        'severity' => ComplaintSeverity::Low,
        'status' => ComplaintStatus::Resolved,
        'reported_at' => now()->subHours(10),
        'resolved_at' => now(),
    ]);

    $stats = $this->service->getComplaintStats();

    expect($stats)
        ->toHaveKeys(['by_type', 'by_severity', 'avg_resolution_hours', 'overdue_count', 'total'])
        ->and($stats['total'])->toBe(2)
        ->and($stats['by_type']['product'])->toBe(1)
        ->and($stats['by_type']['billing'])->toBe(1)
        ->and($stats['by_severity']['high'])->toBe(1)
        ->and($stats['by_severity']['low'])->toBe(1);
});

it('returns overdue complaint count', function (): void {
    $customer = Customer::factory()->for($this->team)->create();

    Complaint::factory()->for($this->team)->for($customer)->create([
        'status' => ComplaintStatus::Open,
        'sla_deadline_at' => now()->subHour(),
    ]);

    Complaint::factory()->for($this->team)->for($customer)->create([
        'status' => ComplaintStatus::Resolved,
        'sla_deadline_at' => now()->subHour(),
    ]);

    $stats = $this->service->getComplaintStats();

    expect($stats['overdue_count'])->toBe(1);
});

it('returns top customers by revenue', function (): void {
    $customer1 = Customer::factory()->for($this->team)->create(['name' => 'Big Spender']);
    $customer2 = Customer::factory()->for($this->team)->create(['name' => 'Small Buyer']);

    Order::factory()->for($this->team)->for($customer1)->count(3)->create(['total' => 50000]);
    Order::factory()->for($this->team)->for($customer2)->create(['total' => 10000]);

    $topCustomers = $this->service->getTopCustomers(10);

    expect($topCustomers)->toHaveCount(2)
        ->and($topCustomers[0]['name'])->toBe('Big Spender')
        ->and($topCustomers[0]['revenue'])->toBe(150000.0)
        ->and($topCustomers[0]['orders_count'])->toBe(3);
});

it('returns empty KPIs when no data exists', function (): void {
    $kpis = $this->service->getKpis(1);

    expect($kpis['monthly_revenue'])->toBe(0.0)
        ->and($kpis['active_quotes'])->toBe(0)
        ->and($kpis['conversion_rate'])->toBe(0.0)
        ->and($kpis['avg_deal_size'])->toBe(0.0);
});
