<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\GoogleAdsReport;
use App\Services\GoogleAdsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = new GoogleAdsService();
});

it('stores google ads report for a campaign', function (): void {
    $campaign = Campaign::factory()->create();
    $googleAdsData = [
        'metadata' => ['location' => 'US', 'language' => 'en'],
        'keyword_data' => [
            ['keyword' => 'test keyword', 'search_volume' => 1000],
        ],
        'statistics' => ['total_keywords' => 1],
    ];

    $report = $this->service->storeGoogleAdsReport($campaign, $googleAdsData);

    expect($report)->toBeInstanceOf(GoogleAdsReport::class)
        ->and($report->campaign_id)->toBe($campaign->id)
        ->and($report->metadata)->toBe($googleAdsData['metadata'])
        ->and($report->keyword_data)->toBe($googleAdsData['keyword_data']);
});

it('stores multiple reports for different dates', function (): void {
    $campaign = Campaign::factory()->create();

    $firstData = [
        'metadata' => ['location' => 'US'],
        'keyword_data' => [['keyword' => 'first', 'search_volume' => 100]],
    ];

    $secondData = [
        'metadata' => ['location' => 'UK'],
        'keyword_data' => [['keyword' => 'second', 'search_volume' => 200]],
    ];

    $firstReport = $this->service->storeGoogleAdsReport($campaign, $firstData, Carbon::parse('2025-10-20'));
    $secondReport = $this->service->storeGoogleAdsReport($campaign, $secondData, Carbon::parse('2025-10-21'));

    expect($firstReport->id)->not->toBe($secondReport->id)
        ->and(GoogleAdsReport::query()->where('campaign_id', $campaign->id)->count())->toBe(2);
});

it('retrieves latest report for a campaign', function (): void {
    $campaign = Campaign::factory()->create();

    GoogleAdsReport::factory()->for($campaign)->create(['report_date' => Carbon::today()->subDays(5)]);
    $latestReport = GoogleAdsReport::factory()->for($campaign)->create(['report_date' => Carbon::today()]);

    $result = $this->service->getLatestReport($campaign);

    expect($result->id)->toBe($latestReport->id);
});

it('retrieves reports for date range', function (): void {
    $campaign = Campaign::factory()->create();
    $startDate = Carbon::today()->subDays(10);
    $endDate = Carbon::today();

    GoogleAdsReport::factory()->for($campaign)->create(['report_date' => $startDate->copy()->addDays(2)]);
    GoogleAdsReport::factory()->for($campaign)->create(['report_date' => $startDate->copy()->addDays(5)]);
    GoogleAdsReport::factory()->for($campaign)->create(['report_date' => $startDate->copy()->subDays(5)]); // Outside range

    $reports = $this->service->getReportsForDateRange($campaign, $startDate, $endDate);

    expect($reports)->toHaveCount(2);
});

it('calculates keyword performance analysis', function (): void {
    $campaign = Campaign::factory()->create();
    $keywordData = [
        ['keyword' => 'test', 'search_volume' => 1000, 'difficulty' => 50, 'competition' => 0.5, 'suggested_bid' => 2.5],
        ['keyword' => 'example', 'search_volume' => 2000, 'difficulty' => 60, 'competition' => 0.6, 'suggested_bid' => 3.0],
    ];

    GoogleAdsReport::factory()->for($campaign)->create([
        'report_date' => Carbon::today()->subDays(5),
        'keyword_data' => $keywordData,
    ]);

    GoogleAdsReport::factory()->for($campaign)->create([
        'report_date' => Carbon::today()->subDays(10),
        'keyword_data' => $keywordData,
    ]);

    $analysis = $this->service->getKeywordPerformanceAnalysis($campaign, 30);

    expect($analysis)->toBeArray()
        ->and($analysis)->toHaveCount(2)
        ->and($analysis[0])->toHaveKey('keyword')
        ->and($analysis[0])->toHaveKey('avg_search_volume');
});

it('returns keyword statistics for a campaign', function (): void {
    $campaign = Campaign::factory()->create();

    GoogleAdsReport::factory()->for($campaign)->create([
        'report_date' => Carbon::today(),
        'keyword_data' => [
            ['keyword' => 'high volume', 'search_volume' => 5000, 'difficulty' => 70, 'competition' => 0.8],
            ['keyword' => 'low comp', 'search_volume' => 500, 'difficulty' => 30, 'competition' => 0.2],
        ],
    ]);

    $stats = $this->service->getKeywordStatistics($campaign);

    expect($stats)->toBeArray()
        ->and($stats['total_keywords'])->toBe(2)
        ->and($stats['high_volume_keywords'])->toBe(1)
        ->and($stats['low_competition_keywords'])->toBe(1);
});

it('returns empty statistics when no reports exist', function (): void {
    $campaign = Campaign::factory()->create();

    $stats = $this->service->getKeywordStatistics($campaign);

    expect($stats)->toBeArray()
        ->and($stats['total_keywords'])->toBe(0)
        ->and($stats['avg_search_volume'])->toBe(0);
});
